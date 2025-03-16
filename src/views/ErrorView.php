<?php

namespace Views;

use Views\Utils;

/**
 * Classe concreta ErrorView che estende BaseView
 * Gestisce la visualizzazione delle pagine di errore
 */
class ErrorView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/error.html");
    }
    
    /**
     * Renderizza la pagina di errore con un messaggio personalizzato
     * 
     * @param array $data Dati da visualizzare, inclusi codice di errore e messaggio
     */
    public function render(array $data = []): void
    {
        parent::render();
        
        $errorCode = isset($data['code']) ? intval($data['code']) : 500;
        $errorMessage = isset($data['message']) ? $data['message'] : 'Si è verificato un errore';
        
        // Gestione di errori specifici
        if ($errorCode === 401 || $errorCode === 403) {
            $this->renderUnauthorizedError($data);
        } else if ($errorCode === 404) {
            $this->renderNotFoundError($data);
        } else {
            $this->renderGenericError($errorMessage, $errorCode);
        }
    }
    
    /**
     * Renderizza un errore di accesso non autorizzato
     */
    public function renderUnauthorizedError(array $data = []): void
    {
        // Imposta titolo specifico per errore di accesso
        Utils::replaceTemplateContent(
            $this->dom,
            "error-title-template",
            "<h1>Accesso richiesto</h1>"
        );
        
        // Imposta messaggio di errore specifico
        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h3>È necessario effettuare l'accesso per visualizzare questa pagina</h3>"
        );
        
        // Svuota i placeholder per i contenuti di altri tipi di errore
        Utils::replaceTemplateContent(
            $this->dom,
            "not-found-error-content",
            ""
        );
        
        Utils::replaceTemplateContent(
            $this->dom,
            "generic-error-content",
            ""
        );
        
        // Prepara i link di login/register con redirect
        $redirectTo = '';
        if (isset($_SESSION['login_redirect'])) {
            $redirectTo = '?redirect=' . urlencode($_SESSION['login_redirect']);
        }
        
        $loginButtons = '
        <div class="access-error-container">
            <p>Per accedere a questa pagina è necessario effettuare il login.</p>
            <div class="error-actions mt-4 flex gap-4">
                <a href="login.php'.$redirectTo.'" class="nav-button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Accedi
                </a>
                <a href="register.php" class="nav-button secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <line x1="19" y1="8" x2="19" y2="14" />
                        <line x1="22" y1="11" x2="16" y2="11" />
                    </svg>
                    Registrati
                </a>
            </div>
        </div>';
        
        // Inserisci contenuto per l'errore di accesso
        Utils::replaceTemplateContent(
            $this->dom,
            "access-error-content", 
            $loginButtons
        );
        
        // Aggiungi un'azione aggiuntiva per tornare alla pagina precedente
        Utils::replaceTemplateContent(
            $this->dom,
            "error-additional-action",
            '<a href="javascript:history.back()" class="nav-button secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Torna Indietro
            </a>'
        );
        
        echo $this->dom->saveHTML();
    }
    
    /**
     * Renderizza un errore 404 (pagina non trovata)
     */
    public function renderNotFoundError(array $data = []): void
    {
        // Imposta titolo specifico per errore 404
        Utils::replaceTemplateContent(
            $this->dom,
            "error-title-template",
            "<h1>Pagina non trovata</h1>"
        );
        
        // Imposta messaggio di errore specifico
        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h3>La pagina che stai cercando non esiste</h3>"
        );
        
        // Svuota i placeholder per i contenuti di altri tipi di errore
        Utils::replaceTemplateContent(
            $this->dom,
            "access-error-content",
            ""
        );
        
        Utils::replaceTemplateContent(
            $this->dom,
            "generic-error-content",
            ""
        );
        
        echo $this->dom->saveHTML();
    }
    
    /**
     * Renderizza un errore generico
     */
    public function renderGenericError(string $message, int $code = 500): void
    {
        // Imposta messaggio di errore specifico
        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h3>" . htmlspecialchars($message) . "</h3>"
        );
        
        // Svuota i placeholder per i contenuti di altri tipi di errore
        Utils::replaceTemplateContent(
            $this->dom,
            "access-error-content",
            ""
        );
        
        Utils::replaceTemplateContent(
            $this->dom,
            "not-found-error-content",
            ""
        );
        
        echo $this->dom->saveHTML();
    }
}