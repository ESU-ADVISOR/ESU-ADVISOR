<?php

namespace Controllers;

use Models\UserModel;
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
        // Salva la pagina originale se fornita come parametro 'redirect'
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
            $this->view->render(["errors" => ["User is not registered"]]);
            return;
        }

        if (!$this->model->authenticate($this->model->findByUsername($username)->getEmail(), $password)) {
            $this->view->render(["errors" => ["Invalid username or password"]]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION["username"] = $username;

        // Debug - stampa il valore di login_redirect se esiste
        error_log("Redirect value: " . (isset($_SESSION['login_redirect']) ? $_SESSION['login_redirect'] : 'none'));

        // Reindirizza alla pagina originale se disponibile, altrimenti alla home
        if (isset($_SESSION['login_redirect']) && !empty($_SESSION['login_redirect'])) {
            $redirectPage = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']); // Pulizia
            header("Location: $redirectPage");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}