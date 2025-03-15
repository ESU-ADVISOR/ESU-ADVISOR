<?php
session_set_cookie_params([
    'lifetime' => 7200,
    'path' => '/',
    'domain' => '',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
    session_unset();
    session_destroy();

    if (strpos($_SERVER['PHP_SELF'], 'index.php') === false) {
        header('Location: login.php');
        exit;
    }
}

$_SESSION['last_activity'] = time();
