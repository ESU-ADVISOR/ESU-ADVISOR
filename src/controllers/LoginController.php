<?php

namespace Controllers;

use Models\UserModel;
use Models\PreferenzeUtenteModel;
use Views\LoginView;

class LoginController implements BaseController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->view = new LoginView();
    }

    public function handleGETRequest(array $get = []): void
    {
        if (isset($get['redirect']) && !empty($get['redirect'])) {
            $_SESSION['login_redirect'] = $get['redirect'];
        }
        
        $this->view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $username = $post["username"];
        $password = $post["password"];

        if (!$this->model->findByUsername($username)) {
            $this->view->render([
                "errors" => ["Utente non registrato"],
                "formData" => $post
            ]);
            return;
        }

        if (!$this->model->authenticate($this->model->findByUsername($username)->getUsername(), $password)) {
            $this->view->render([
                "errors" => ["Username o password non validi"],
                "formData" => ["username" => $username]
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION["username"] = $username;

        $userPreferences = PreferenzeUtenteModel::findByUsername($username);
        $preferences = [
            'theme' => 'sistema',
            'textSize' => 'medio',
            'iconSize' => 'medio', 
            'fontFamily' => 'normale'
        ];

        if ($userPreferences) {
            $userPreferences->syncToSession();
            
            if ($userPreferences->getTema()) {
                $preferences['theme'] = $userPreferences->getTema()->value;
            }
            if ($userPreferences->getDimensioneTesto()) {
                $preferences['textSize'] = $userPreferences->getDimensioneTesto()->value;
            }
            if ($userPreferences->getDimensioneIcone()) {
                $preferences['iconSize'] = $userPreferences->getDimensioneIcone()->value;
            }
            if ($userPreferences->getModificaFont()) {
                $preferences['fontFamily'] = $userPreferences->getModificaFont()->value;
            }
        }

        $redirectPage = 'index.php';
        if (isset($_SESSION['login_redirect']) && !empty($_SESSION['login_redirect'])) {
            $redirectPage = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']);
        }

        ?>
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <title>Login Effettuato</title>
        </head>
        <body>
            <script>
                var serverPrefs = <?php echo json_encode($preferences); ?>;
                
                if (serverPrefs.theme === "scuro") {
                    localStorage.setItem("theme", "dark");
                    document.documentElement.classList.add("theme-dark");
                    document.documentElement.classList.remove("theme-light");
                } else if (serverPrefs.theme === "chiaro") {
                    localStorage.setItem("theme", "light");
                    document.documentElement.classList.remove("theme-dark");
                    document.documentElement.classList.add("theme-light");
                } else {
                    localStorage.removeItem("theme");
                    document.documentElement.classList.remove("theme-dark", "theme-light");
                }

                localStorage.setItem("textSize", serverPrefs.textSize);
                localStorage.setItem("fontFamily", serverPrefs.fontFamily);
                localStorage.setItem("iconSize", serverPrefs.iconSize);
                
                document.documentElement.classList.remove(
                    "text-size-piccolo", "text-size-medio", "text-size-grande"
                );
                document.documentElement.classList.add("text-size-" + serverPrefs.textSize);
                
                document.documentElement.classList.remove("font-normale", "font-dislessia");
                document.documentElement.classList.add("font-" + serverPrefs.fontFamily);
                
                document.documentElement.classList.remove(
                    "icon-size-piccolo", "icon-size-medio", "icon-size-grande"
                );
                document.documentElement.classList.add("icon-size-" + serverPrefs.iconSize);
                
                window.location.href = "<?php echo htmlspecialchars($redirectPage); ?>";
            </script>
        </body>
        </html>
        <?php
        exit();
    }
}