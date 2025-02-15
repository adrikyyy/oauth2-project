<?php
session_start();
require_once __DIR__ . '/../server/config.php';

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['success' => false, 'message' => 'CSRF token validation failed']));
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['password'];

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Generate OAuth2 authorization code
           // Generate OAuth2 authorization code
$auth_code = bin2hex(random_bytes(16));

// Ambil client_id, redirect_uri, scope dari config
$client_config = OAuth2Config::getClientConfig();
$client_id = 1; // atau $client_config['client_id'] jika sudah ada di DB
$redirect_uri = $client_config['redirect_uri'] ?? 'http://localhost:8000/callback.php';
$scope = $client_config['scope'] ?? '';

$stmt = $db->prepare("
    INSERT INTO oauth_auth_codes 
    (authorization_code, client_id, user_id, redirect_uri, expires, scope)
    VALUES (?, ?, ?, ?, ?, ?)
");
$expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
$stmt->execute([$auth_code, $client_id, $user['id'], $redirect_uri, $expires, $scope]);


            echo json_encode([
  'success' => true, 
  'redirect' => '/callback.php?code=' . $auth_code
]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form id="loginForm" method="POST" class="login-form">
            <h2>Login</h2>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary">Login</button>
            <div id="error-message" class="error-message"></div>
        </form>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/login.php', {
      method: 'POST',
      body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.parent.location.href = data.redirect;
            } else {
                document.getElementById('error-message').textContent = data.message;
            }
        })
        .catch(error => {
            document.getElementById('error-message').textContent = 'An error occurred';
        });
    });
    </script>
</body>
</html>