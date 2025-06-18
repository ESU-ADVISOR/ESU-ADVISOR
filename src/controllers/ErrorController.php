<?php

namespace Controllers;

use Views\ErrorView;

class ErrorController implements BaseController
{
    private $view;

    public function __construct()
    {
        $this->view = new ErrorView();
    }

    public function handleGETRequest(array $get = []): void
    {
        $errorTitle = isset($get['title']) ? $get['title'] : null;;
        $errorCode = isset($get['code']) ? intval($get['code']) : (isset($_SERVER['REDIRECT_STATUS']) ? intval($_SERVER['REDIRECT_STATUS']) : 500);
        $errorMessage = isset($get['message']) ? $get['message'] : null;
        if (empty($errorMessage) || empty($errorTitle)) {
            switch ($errorCode) {
                case 404:
                    empty($errorTitle) ? $errorTitle = "Pagina non trovata" : null;
                    empty($errorMessage) ? $errorMessage = "La pagina che stai cercando non è stata trovata" : null;
                    break;
                case 401:
                case 403:
                    empty($errorTitle) ? $errorTitle = "Accesso richiesto" : null;
                    empty($errorMessage) ? $errorMessage =  "È necessario effettuare l'accesso per visualizzare questa pagina" : null;
                    break;
                case 500:
                default:
                    empty($errorTitle) ? $errorTitle = "Qualcosa è andato storto" : null;
                    empty($errorMessage) ? $errorMessage = "Si è verificato un errore interno del server" : null;
                    break;
            }
        }

        if (isset($get['page']) && !empty($get['page']) && $get['page'] != "error.php") {
            $_SESSION['login_redirect'] = $get['page'];
        }

        $this->view->render([
            'title' => $errorTitle,
            'code' => $errorCode,
            'message' => $errorMessage,
            'page' => isset($get['page']) ? $get['page'] : null
        ]);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "error" => "Richiesta POST non consentita",
        ]);
        exit();
    }
}
