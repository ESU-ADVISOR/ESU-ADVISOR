<?php
require_once "../src/config.php";

use Controllers\ProfileController;

$controller = new ProfileController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: error.php?code=401&page=profile.php");
        exit();
    }
}