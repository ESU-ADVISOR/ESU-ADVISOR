<?php

namespace Controllers;

use Models\UserModel;
use Views\RegisterView;

class RegisterController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new RegisterView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $username = $post["username"];
        $password = $post["password"];
        $dataNascita = $post["dataNascita"];
        $view = new RegisterView();

        try {
            if (UserModel::isUsernameTaken($username)) {
                $view->render([
                    "errors" => ["Registration failed: Username already exists"]
                ]);
                return;
            }

            $new_user = new UserModel([
                "username" => $username,
                "password" => $password,
                "dataNascita" => $dataNascita,
            ]);

            if ($new_user->saveToDB()) {
                $view->render(["success" => "Registration successful!"]);
                return;
            } else {
                $view->render([
                    "errors" => ["Registration failed: Could not save to the database"],
                ]);
            }
        } catch (\Exception $e) {
            $view->render([
                "errors" => ["Registration failed: " . $e->getMessage()],
            ]);
        }
    }
}
