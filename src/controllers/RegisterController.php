<?php
namespace Controllers;

use Models\UserModel;
use Views\RegisterView;

class RegisterController implements BaseController
{
    private $view;

    public function __construct()
    {
        $this->view = new RegisterView();
    }

    public function handleGETRequest(array $get = []): void
    {
        $this->view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $username = $post["username"];
        $email = $post["email"];
        $password = $post["password"];
        $dataNascita = $post["dataNascita"];

        if (UserModel::isEmailTaken($email)) {
            $this->view->render(["errors" => ["Email is already taken"]]);
            return;
        }

        try {
            $new_user = new UserModel([
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "dataNascita" => $dataNascita,
            ]);

            if ($new_user->saveToDB()) {
                echo "hi";
                $this->view->render(["success" => "Registration successful!"]);
                return;
            } else {
                echo "ho on";
                $this->view->render([
                    "error" => "Registration failed: ",
                    // "Registration failed: " . $this->model->getLastError(),
                ]);
            }
        } catch (\Exception $e) {
            $this->view->render([
                "error" => "Registration failed: " . $e->getMessage(),
            ]);
        }
    }
}
