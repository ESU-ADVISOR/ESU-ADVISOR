<?php
session_start();
require_once "../src/config.php";
use Controllers\IndexController;

$controller = new IndexController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    $controller->handleGETRequest($_GET);
}
?>
