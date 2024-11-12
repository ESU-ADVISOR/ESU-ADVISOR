<?php
namespace Controllers;

use Models\MenseModel;
use Models\MenuModel;
use Views\IndexView;

class IndexController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $model = new MenseModel();
        $mense = $model->getAllMense();

        $menuModel = new MenuModel();
        $menu = $menuModel->getAllMenu();

        $view = new IndexView();

        $view->render([
            "mense" => array_column($mense, "nome"),
            "menu" => array_column($menu, "nome"),
        ]);
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
