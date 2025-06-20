<?php
require_once "../src/config.php";

use Controllers\ReviewController;

$controller = new ReviewController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
        $controller->handlePOSTRequest($_POST);
    } else {
        header("Location: error.php?code=401&page=review.php");
        exit();
    }
} else {
    if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: error.php?code=401&page=review.php");
        exit();
    }
}