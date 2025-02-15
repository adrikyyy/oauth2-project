<?php
// server/rsa.php
class RSAEncryption {
    private $privateKey;
    private $publicKey;
    
    public function __construct() {
        $this->privateKey = RSAKeys::getPrivateKey();
        $this->publicKey = RSAKeys::getPublicKey();
        
        if (!$this->privateKey || !$this->publicKey) {
            $this->generateKeys();
        }
    }
    
    private function generateKeys() {
        // Generate new key pair
        $config = array(
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        
        $res = openssl_pkey_new($config);
        
        // Extract private key
        openssl_pkey_export($res, $privateKey);
        
        // Extract public key
        $publicKey = openssl_pkey_get_details($res);
        $publicKey = $publicKey["key"];
        
        // Save keys to files
        file_put_contents(__DIR__ . '/keys/private.pem', $privateKey);
        file_put_contents(__DIR__ . '/keys/public.pem', $publicKey);
        
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }
    
    public function encrypt($data) {
        $encrypted = '';
        if (openssl_public_encrypt($data, $encrypted, $this->publicKey)) {
            return base64_encode($encrypted);
        }
        throw new Exception('Encryption failed');
    }
    
    public function decrypt($data) {
        $decrypted = '';
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->privateKey)) {
            return $decrypted;
        }
        throw new Exception('Decryption failed');
    }
}