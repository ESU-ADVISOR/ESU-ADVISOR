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
                "errors" => ["<span lang='en'>Username</span> o <span lang='en'>password</span> non validi"],
                "formData" => ["username" => $username]
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION["username"] = $username;

        $userPreferences = PreferenzeUtenteModel::findByUsername($username);
        if ($userPreferences) {
            $userPreferences->syncToSession();
        }

        $redirectPage = 'index.php';
        if (isset($_SESSION['login_redirect']) && !empty($_SESSION['login_redirect'])) {
            $redirectPage = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']);
        }

        header("Location: " . $redirectPage);
        exit();
    }
}