<?php
require_once "../src/config.php";

// This endpoint is AJAX only
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    header('HTTP/1.1 403 Forbidden');
    echo "Direct access not allowed";
    exit;
}

$isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);
$previousStatus = isset($_GET['status']) ? $_GET['status'] === 'true' : null;
$needsReload = $previousStatus !== null && $previousStatus !== $isLoggedIn;

header('Content-Type: application/json');
echo json_encode([
    'logged_in' => $isLoggedIn,
    'reload' => $needsReload
]);
exit;
