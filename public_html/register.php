<?php
require_once "../src/config.php";

use Controllers\RegisterController;

$controller = new RegisterController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $password = "";
    $errors = [];

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $dataNascita = trim($_POST["birth_date"] ?? "");

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

    if (empty($username)) {
        $errors[] = "Username is required.";
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

    if (empty($dataNascita)) {
        $errors[] = "Data di nascita is required.";
    } else {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataNascita)) {
            $errors[] = "Please enter a valid data di nascita.";
        }
    }

    if (empty($errors)) {
        $data = [
            "username" => $username,
            "password" => $password,
            "dataNascita" => $dataNascita,
        ];
        $controller->handlePOSTRequest($data);
    } else {
        http_response_code(400);
        $controller->handleGETRequest([
            "status" => "error",
            "errors" => $errors,
        ]);

        exit();
    }
} else {
    $controller->handleGETRequest($_GET);
}
