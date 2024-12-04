<?php
session_start();
require_once "../src/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // $controller->handlePOSTRequest($_POST);
} else {
    if (isset($_SESSION["email"]) && !empty($_SESSION["email"])) {
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
