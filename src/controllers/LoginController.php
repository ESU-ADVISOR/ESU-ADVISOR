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
        $username = $post["username"];
        $password = $post["password"];

        if (!$this->model->findByUsername($username)) {
            $this->view->render(["errors" => ["User is not registered"]]);
            return;
        }

        if (!$this->model->authenticate($this->model->findByUsername($username)->getEmail(), $password)) {
            $this->view->render(["errors" => ["Invalid username or password"]]);
            echo "Invalid username or password";
            return;
        }

        session_regenerate_id(true);

        $_SESSION["username"] = $username;

        header("Location: index.php");
    }
}
