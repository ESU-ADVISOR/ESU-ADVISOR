<?php

namespace Controllers;

use Models\UserModel;
use Views\SettingsView;

class SettingsController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new SettingsView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new SettingsView();
        print_r($post);
        if (isset($_POST['delete_account']) && isset($_SESSION["email"])) {
            $user = UserModel::findByEmail($_SESSION["email"]);
            if ($user === null) {
                $view->render([
                    "errors" => ["User not found"],
                ]);
                exit();
            }

            if ($user->deleteFromDB()) {
                session_destroy();
                header("Location: index.php");
                exit();
            } else {
                $view->render([
                    "errors" => ["Registration failed: Could not remove from the database"],
                ]);
                exit();
            }
        } else {

            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "error" => "POST request not allowed",
            ]);

            exit();
        }
    }
}
