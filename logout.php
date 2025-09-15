<?php
session_start();
session_destroy();

// Get the referring page or default to index.php
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

// Make sure we don't redirect to logout.php itself or login.php
if (strpos($redirect_url, 'logout.php') !== false || strpos($redirect_url, 'login.php') !== false) {
    $redirect_url = 'index.php';
}

header("Location: " . $redirect_url);
exit();
?>
