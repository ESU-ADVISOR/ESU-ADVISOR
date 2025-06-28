<?php
require_once "../src/config.php";

use Controllers\MensaController;

$controller = new MensaController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    if (!empty($_GET) && isset($_GET["mensa"])) {
        $controller->handleGETRequest($_GET);
    } else {
        header("Location: index.php");
    }
}
