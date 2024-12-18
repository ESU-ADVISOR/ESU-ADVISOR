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
        $email = $post["email"];
        $password = $post["password"];
        $dataNascita = $post["dataNascita"];
        $view = new RegisterView();
        if (UserModel::isEmailTaken($email)) {
            $view->render(["errors" => ["Email is already taken"]]);
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
