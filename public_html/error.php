<?php
require_once "../src/config.php";

use Controllers\ErrorController;

$controller = new ErrorController();

// Determina il codice di errore dal parametro GET o dal server
$errorCode = isset($_GET['code']) ? intval($_GET['code']) : 
            (isset($_SERVER['REDIRECT_STATUS']) ? intval($_SERVER['REDIRECT_STATUS']) : 500);

// Determina il messaggio di errore
$errorMessage = isset($_GET['message']) ? $_GET['message'] : null;

// Se non c'è un messaggio esplicito, usa quelli predefiniti in base al codice
if (empty($errorMessage)) {
    switch ($errorCode) {
        case 404:
            $errorMessage = "La pagina che stai cercando non è stata trovata";
            break;
        case 401:
        case 403:
            $errorMessage = "Non hai i permessi per accedere a questa pagina";
            break;
        case 500:
            $errorMessage = "Si è verificato un errore interno del server";
            break;
        default:
            $errorMessage = "Si è verificato un errore";
            break;
    }
}

// Ottieni informazioni sulla pagina tentata di accedere, se disponibile
$attemptedPage = isset($_GET['page']) ? $_GET['page'] : '';

if ($errorCode === 404) {
    $controller->handle404();
} else if ($errorCode === 401 || $errorCode === 403) {
    // Memorizza la pagina che l'utente stava cercando di accedere per un potenziale futuro reindirizzamento
    if (!empty($attemptedPage)) {
        $_SESSION['login_redirect'] = $attemptedPage;
    }
    $controller->handleUnauthorized();
} else {
    $controller->handleError($errorMessage, $errorCode);
}