<?php
session_start();
require_once "../src/config.php";
use Controllers\SettingsController;

$controller = new SettingsController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    if (isset($_SESSION["email"]) && !empty($_SESSION["email"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
