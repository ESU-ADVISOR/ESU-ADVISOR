<?php
session_set_cookie_params([
    'lifetime' => 7200,
    'path' => '/',
    'domain' => '',
    'httponly' => true,
    'samesite' => 'Lax'
]);

$isIndexPage = strpos($_SERVER['PHP_SELF'], 'index.php') !== false;

if ($isIndexPage) {
    session_cache_limiter('public');
} else {
    session_cache_limiter('nocache');
}

session_start();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
    session_unset();
    session_destroy();

    if (!$isIndexPage) {
        header('Location: login.php');
        exit;
    }
}

$_SESSION['last_activity'] = time();
