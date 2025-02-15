<?php
// public/logout.php
session_start();
require_once '../server/config.php';

// Revoke tokens if they exist
if (isset($_SESSION['access_token'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM oauth_access_tokens WHERE access_token = ?");
        $stmt->execute([$_SESSION['access_token']]);

        if (isset($_SESSION['refresh_token'])) {
            $stmt = $db->prepare("DELETE FROM oauth_refresh_tokens WHERE refresh_token = ?");
            $stmt->execute([$_SESSION['refresh_token']]);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

// Destroy session
session_destroy();

// Redirect to homepage
header('Location: index.php');
exit;
?>