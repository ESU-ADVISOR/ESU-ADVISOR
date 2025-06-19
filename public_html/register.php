<?php
require_once "../src/config.php";

use Controllers\RegisterController;

$controller = new RegisterController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $password = "";
    $errors = [];

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");
    $dataNascita = trim($_POST["birth_date"] ?? "");

    if (empty($username)) {
        $errors[] = "È necessario lo <span lang='en'>username</span>.";
    } else {
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Lo <span lang='en'>username</span> deve essere compreso tra 3 e 50 caratteri.";
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] =
                "Lo <span lang='en'>username</span> può contenere solo lettere, numeri e <span lang='en'>underscore</span>.";
        }
    }

    if (empty($dataNascita) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataNascita)) {
        $errors[] = "Per favore inserisci una data di nascita valida.";
    } else {
        $birthDate = new DateTime($dataNascita);
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($birthDate > $today) {
            $errors[] = "La data di nascita non può essere nel futuro.";
        }
    }

    if (empty($password)) {
        $errors[] = "È necessaria la <span lang='en'>password</span>.";
    } else {
        if (strlen($password) < 8) {
            $errors[] = "La <span lang='en'>password</span> deve essere di almeno 8 caratteri.";
        }
        if (
            !preg_match(
                '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                $password
            )
        ) {
            $errors[] =
                "La <span lang='en'>password</span> deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&).";
        }
    }

    if (empty($confirmPassword)) {
        $errors[] = "È necessario confermare la <span lang='en'>password</span>.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Le <span lang='en'>password</span> non corrispondono.";
    }

    if (empty($errors)) {
        $data = [
            "username" => $username,
            "password" => $password,
            "birth_date" => $dataNascita,
            "confirm_password" => $confirmPassword,
        ];
        $controller->handlePOSTRequest($data);
    } else {
        http_response_code(400);

        $controller->handleGETRequest([
            "status" => "error",
            "errors" => $errors,
            "formData" => [
                "username" => $username,
                "birth_date" => $dataNascita,
                "password" => $password,
                "confirm_password" => $confirmPassword,
            ],
        ]);

        exit();
    }
} else {
    $controller->handleGETRequest($_GET);
}
