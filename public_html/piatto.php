<?php
require_once "../src/config.php";

use Controllers\PiattoController;

$controller = new PiattoController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    if (!empty($_GET) && isset($_GET["nome"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: index.php");
    }
}
