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
        $this->view->render();
    }

    public function handlePOSTRequest(array $post = [])
    {
        $username = $post["username"];
        $password = $post["password"];

        if (!$this->model->isUserValid($username, $password)) {
            $this->view->render(["errors" => ["Invalid username or password"]]);
            return;
        }

        if (!$this->model->authenticate($username, $password)) {
            $this->view->render(["errors" => ["Invalid username or password"]]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION["username"] = $username;

        header("Location: index.php");
    }
}
