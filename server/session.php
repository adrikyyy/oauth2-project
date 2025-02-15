<?php
// server/session.php
class SessionManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 1);
            
            session_start();
        }
    }
    
    public function regenerateSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
    
    public function validateSession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['access_token'])) {
            return false;
        }
        
        $tokenHandler = new TokenHandler();
        return $tokenHandler->validateAccessToken($_SESSION['access_token']);
    }
    
    public function clearSession() {
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        session_destroy();
    }
    
    public function setUserSession($userId, $accessToken, $refreshToken) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['access_token'] = $accessToken;
        $_SESSION['refresh_token'] = $refreshToken;
        $_SESSION['last_activity'] = time();
        
        $this->regenerateSession();
    }
}