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
}

$_SESSION['last_activity'] = time();
