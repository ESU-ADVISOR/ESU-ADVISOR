<?php

namespace Views;

use Views\Utils;

class ErrorView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/error.html");
    }

    public function render(array $data = []): void
    {
        if (isset($data['page']) && !empty($data['page'])) {
            $originalPage = basename($data['page'], '.php');
            $pageNames = [
                'review' => 'Review',
                'profile' => 'Profilo'
            ];

            if (isset($pageNames[$originalPage])) {
                $this->setBreadcrumb(['current' => $pageNames[$originalPage]]);
                $this->currentPage = $originalPage;
            } else {
                $this->setBreadcrumb(['current' => 'Error']);
            }
        } else {
            $this->setBreadcrumb(['current' => 'Error']);
        }

        parent::render();
        $title = isset($data['title']) ? $data['title'] : "<h1>Qualcosa è andato storto</h1>";
        $errorCode = isset($data['code']) ? intval($data['code']) : 500;
        $errorMessage = isset($data['message']) ? $data['message'] : 'Si è verificato un errore';

        Utils::replaceTemplateContent(
            $this->dom,
            "error-title-template",
            "<h1>" . $title . "</h1>"
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h2>" . $errorMessage . "</h2>"
        );

        $redirectTo = '';
        if (isset($_SESSION['login_redirect'])) {
            $redirectTo = '?redirect=' . urlencode($_SESSION['login_redirect']);
        }

        if ($errorCode == 401 || $errorCode == 403) {
            $loginButtons = '
            <div class="access-error-container">
                <p>Per accedere a questa pagina è necessario effettuare il login.</p>
                <div class="error-actions">
                    <a href="login.php' . $redirectTo . '" class="nav-button primary">
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

            Utils::replaceTemplateContent(
                $this->dom,
                "error-content-template",
                $loginButtons
            );
        }

        echo $this->dom->saveHTML();
    }
}
