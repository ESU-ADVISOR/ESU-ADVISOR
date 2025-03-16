<?php

namespace Controllers;

use Views\BaseView;

class ErrorController implements BaseController
{
    private $view;

    public function __construct()
    {
        $this->view = new \Views\ErrorView();
    }

    public function handleGETRequest(array $get = []): void
    {
        $errorCode = isset($get['code']) ? intval($get['code']) : 500;
        $errorMessage = isset($get['message']) ? $get['message'] : "Si è verificato un errore";
        
        // Salva il riferimento alla pagina che l'utente tentava di accedere
        if (isset($get['page']) && !empty($get['page'])) {
            $_SESSION['login_redirect'] = $get['page'];
        }
        
        $this->view->render([
            'code' => $errorCode,
            'message' => $errorMessage
        ]);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        // Reindirizza alla GET per semplicità
        $this->handleGETRequest($post);
    }
    
    /**
     * Gestisce diversi tipi di errore in base al codice HTTP
     */
    public function handleError(string $message, int $code = 500): void
    {
        $this->view->render([
            'code' => $code,
            'message' => $message
        ]);
    }
    
    /**
     * Gestisce specificamente errori 404 (pagina non trovata)
     */
    public function handle404(): void
    {
        $this->view->render([
            'code' => 404,
            'message' => 'La pagina richiesta non è stata trovata'
        ]);
    }
    
    /**
     * Gestisce specificamente errori di accesso non autorizzato
     */
    public function handleUnauthorized(): void
    {
        $this->view->render([
            'code' => 401,
            'message' => 'Devi effettuare il login per accedere'
        ]);
    }
}