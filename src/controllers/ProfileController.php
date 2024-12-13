<?php
namespace Controllers;

use Models\UserModel;
use Views\ProfileView;

class ProfileController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new ProfileView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "error" => "POST request not allowed",
        ]);
        exit();
    }
}
?>
