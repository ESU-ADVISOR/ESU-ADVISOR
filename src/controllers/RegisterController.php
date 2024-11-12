<?php
namespace Controllers;

use Models\UserModel;
use Views\RegisterView;

class RegisterController implements BaseController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->view = new RegisterView();
    }

    public function handleGETRequest(array $get = []): void
    {
        $this->view->render();
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $username = $post["username"];
        $email = $post["email"];
        $password = $post["password"];

        if ($this->model->isEmailTaken($email)) {
            $this->view->render(["errors" => ["Email is already taken"]]);
            return;
        }

        try {
            $this->model->createUser($username, $email, $password);
            $this->view->render(["success" => "Registration successful!"]);
        } catch (\Exception $e) {
            $this->view->render([
                "error" => "Registration failed: " . $e->getMessage(),
            ]);
        }
    }
}
