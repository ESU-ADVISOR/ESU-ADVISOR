<?php
require_once "../src/config.php";

use Controllers\LoginController;

$controller = new LoginController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = [];

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($username)) {
        $errors[] = "È necessario lo <span lang='en'>username</span>.";
    }
    if (empty($password)) {
        $errors[] = "È necessaria la <span lang='en'>password</span>.";
    }

    if (empty($errors)) {
        $data = [
            "username" => $username,
            "password" => $password,
        ];
        $controller->handlePOSTRequest($data);
    } else {
        $controller->handleGETRequest(["errors" => $errors]);
    }
} else {
    $controller->handleGETRequest($_GET);
}
