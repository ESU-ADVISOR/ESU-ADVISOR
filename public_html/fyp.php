<?php
session_start();
require_once "../src/config.php";

use Controllers\ForYouPageController;

$controller = new ForYouPageController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: login.php");
        exit();
    }
}
