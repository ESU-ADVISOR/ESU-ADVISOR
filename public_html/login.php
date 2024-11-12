<?php
session_start();
require_once "../src/config.php";

use Controllers\LoginController;

$controller = new LoginController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $password = "";
    $errors = [];

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($username)) {
        $errors[] = "Username is required.";
    } else {
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Username must be between 3 and 50 characters long.";
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors[] =
                "Username can only contain letters, numbers, underscores, and hyphens.";
        }
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } else {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (
            !preg_match(
                '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                $password
            )
        ) {
            $errors[] =
                "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).";
        }
    }

    if (empty($errors)) {
        $data = [
            "username" => $username,
            "password" => $password,
        ];
        $controller->handlePOSTRequest($data);
    }
} else {
    $controller->handleGETRequest($_GET);
}
