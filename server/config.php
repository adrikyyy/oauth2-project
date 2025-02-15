<?php
// server/config.php
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Required environment variables
$dotenv->required([
    'DB_HOST', 
    'DB_NAME', 
    'DB_USER', 
    'OAUTH_CLIENT_ID', 
    'OAUTH_CLIENT_SECRET'
]);

// Constants
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? false);
define('APP_URL', $_ENV['APP_URL']);

// Error reporting based on environment
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
}

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    
    if (APP_DEBUG) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    } else {
        error_log(json_encode($error));
    }
    
    return true;
});

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Database configuration class (extended)
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO(
                "mysql:host=" . $_ENV['DB_HOST'] . 
                ";dbname=" . $_ENV['DB_NAME'] . 
                ";charset=utf8mb4",
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD'],
                $options
            );
        } catch(PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserialize of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// OAuth2 configuration class (extended)
class OAuth2Config {
    const TOKEN_LIFETIME = 3600; // 1 hour
    const REFRESH_TOKEN_LIFETIME = 1209600; // 14 days
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOGIN_ATTEMPT_TIMEOUT = 300; // 5 minutes
    
    public static function getClientConfig() {
        return [
            'client_id' => $_ENV['OAUTH_CLIENT_ID'],
            'client_secret' => $_ENV['OAUTH_CLIENT_SECRET'],
            'redirect_uri' => $_ENV['OAUTH_REDIRECT_URI'],
            'scope' => 'basic email profile',
            'grant_types' => ['authorization_code', 'refresh_token'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'client_secret_basic'
        ];
    }
    
    public static function validateScope($scope) {
        $allowedScopes = ['basic', 'email', 'profile'];
        $requestedScopes = explode(' ', $scope);
        return !array_diff($requestedScopes, $allowedScopes);
    }
}

// Initialize session with secure defaults
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $_ENV['SECURE_COOKIE'] ?? true);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', $_ENV['SESSION_LIFETIME'] ?? 7200);

    session_set_cookie_params([
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 7200,
        'path' => '/',
        'domain' => '',
        'secure' => $_ENV['SECURE_COOKIE'] ?? true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start(); // Start session only if it's not already started
}
