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
        $this->view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $email = $post["email"];
        $password = $post["password"];

        if (!$this->model->isEmailTaken($email)) {
            $this->view->render(["errors" => ["Email is not registered"]]);
            return;
        }

        if (!$this->model->authenticate($email, $password)) {
            $this->view->render(["errors" => ["Invalid username or password"]]);
            echo "Invalid username or password";
            return;
        }

        session_regenerate_id(true);

        $_SESSION["email"] = $email;

        header("Location: index.php");
    }
}
