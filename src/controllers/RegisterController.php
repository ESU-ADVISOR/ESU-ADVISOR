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
        $dataNascita = $post["birth_date"];
        $view = new RegisterView();

        try {
            if (UserModel::isUsernameTaken($username)) {
                $view->render([
                    "errors" => ["Registrazione fallita: <span lang='en'>username</span> già in uso"],
                    "formData" => $post,
                    "focus" => "username"
                ]);
                return;
            }

            $new_user = new UserModel([
                "username" => $username,
                "password" => $password,
                "dataNascita" => $dataNascita,
            ]);

            if ($new_user->saveToDB()) {
                print_r("hi");
                $view->render(["success" => "Registrazione completata con successo!"]);
                return;
            } else {
                print_r("hi");

                $view->render([
                    "errors" => ["Registrazione fallita: impossibile salvare nel database"],
                    "formData" => $post
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore di registrazione: " . $e->getMessage());

            $view->render([
                "errors" => ["Registrazione fallita: " . $e->getMessage()],
                "formData" => $post
            ]);
        }
    }
}
