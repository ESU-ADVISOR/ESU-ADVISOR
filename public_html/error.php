<?php
require_once "../src/config.php";

use Controllers\ErrorController;

$controller = new ErrorController();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->handlePOSTRequest($_POST);
} else {
    $controller->handleGETRequest($_GET);
}
