<?php
// server/auth.php
require_once 'config.php';
require_once 'rsa.php';

class TokenHandler {
    private $db;
    private $rsa;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->rsa = new RSAEncryption();
    }
    
    public function generateTokens($userId) {
        try {
            // Generate tokens
            $accessToken = $this->generateAccessToken();
            $refreshToken = $this->generateRefreshToken();
            
            // Set expiration times
            $accessTokenExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $refreshTokenExpires = date('Y-m-d H:i:s', strtotime('+14 days'));
            
            // Encrypt tokens using RSA
            $encryptedAccessToken = $this->rsa->encrypt($accessToken);
            $encryptedRefreshToken = $this->rsa->encrypt($refreshToken);
            
            // Store tokens in database
            $this->storeAccessToken($userId, $encryptedAccessToken, $accessTokenExpires);
            $this->storeRefreshToken($userId, $encryptedRefreshToken, $refreshTokenExpires);
            
            return [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 3600,
                'token_type' => 'Bearer'
            ];
        } catch (Exception $e) {
            error_log('Token generation error: ' . $e->getMessage());
            throw new Exception('Error generating tokens');
        }
    }
    
    private function generateAccessToken() {
        return bin2hex(random_bytes(32));
    }
    
    private function generateRefreshToken() {
        return bin2hex(random_bytes(32));
    }
    
    private function storeAccessToken($userId, $token, $expires) {
        $stmt = $this->db->prepare("
            INSERT INTO oauth_access_tokens (access_token, user_id, expires, client_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$token, $userId, $expires, OAuth2Config::getClientConfig()['client_id']]);
    }
    
    private function storeRefreshToken($userId, $token, $expires) {
        $stmt = $this->db->prepare("
            INSERT INTO oauth_refresh_tokens (refresh_token, user_id, expires, client_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$token, $userId, $expires, OAuth2Config::getClientConfig()['client_id']]);
    }
    
    public function refreshAccessToken($refreshToken) {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id 
                FROM oauth_refresh_tokens 
                WHERE refresh_token = ? AND expires > NOW()
            ");
            $stmt->execute([$this->rsa->encrypt($refreshToken)]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception('Invalid refresh token');
            }
            
            // Revoke old refresh token
            $this->revokeRefreshToken($refreshToken);
            
            // Generate new tokens
            return $this->generateTokens($result['user_id']);
            
        } catch (Exception $e) {
            error_log('Token refresh error: ' . $e->getMessage());
            throw new Exception('Error refreshing token');
        }
    }
    
    public function revokeRefreshToken($refreshToken) {
        $stmt = $this->db->prepare("
            DELETE FROM oauth_refresh_tokens 
            WHERE refresh_token = ?
        ");
        $stmt->execute([$this->rsa->encrypt($refreshToken)]);
    }
    
    public function validateAccessToken($accessToken) {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id 
                FROM oauth_access_tokens 
                WHERE access_token = ? AND expires > NOW()
            ");
            $stmt->execute([$this->rsa->encrypt($accessToken)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Token validation error: ' . $e->getMessage());
            return false;
        }
    }
}