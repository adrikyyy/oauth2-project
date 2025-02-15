<?php
session_start();
require_once __DIR__ . '/../server/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth2 Login System</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to OAuth2 Demo</h1>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="user-info">
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <a href="/logout.php" class="btn btn-danger">Logout</a>
                </div>
            <?php else: ?>
                <button id="loginBtn" class="btn btn-primary">Login</button>
            <?php endif; ?>
        </header>

        <main>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="dashboard">
                    <h2>Your Dashboard</h2>
                    <p>You are successfully logged in!</p>
                </div>
            <?php else: ?>
                <div class="welcome-message">
                    <h2>Please login to continue</h2>
                    <p>This is a secure OAuth2 implementation demo.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe src="/login.php"></iframe>
        </div>
    </div>

    <script src="/assets/js/popup.js" defer></script>
</body>
</html>