<?php
require_once "../src/config.php";

use Controllers\SettingsController;

$controller = new SettingsController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    $controller->handleGETRequest($_GET);
}