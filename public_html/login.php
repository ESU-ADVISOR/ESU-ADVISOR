<?php
session_start();
require_once "../src/config.php";

use Controllers\LoginController;

$controller = new LoginController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $email = "";
    $errors = [];

    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email)) {
        $errors[] = "Email address is required.";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $data = [
            "email" => $email,
            "password" => $password,
        ];
        $controller->handlePOSTRequest($data);
    } else {
        $controller->handleGETRequest(["errors" => $errors]);
    }
} else {
    $controller->handleGETRequest($_GET);
}
