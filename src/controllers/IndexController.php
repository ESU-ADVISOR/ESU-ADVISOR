<?php
namespace Controllers;

use Models\MenseModel;
use Models\MenuModel;
use Models\PiattiModel;
use Views\IndexView;

class IndexController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $model = new MenseModel();
        $mense = $model->getAllMense();

        $menuModel = new MenuModel();
        $menu = $menuModel->getAllMenu();

        $piattiModel = new PiattiModel();
        $piatti = $piattiModel->getAllPiatti();

        $view = new IndexView();

        $view->render([
            //test
            //"mense" => array_column($mense, "nome"),
            //"mense" => $mense,
            "mense" => array_map(function($mensa){
                return [
                    'id' => $mensa['id'],
                    'nome' => $mensa['nome']
                ];
            }, $mense),
            //
            "menu" => array_column($menu, "nome"),
            "piatti" => array_map(function($piatto){
                return [
                    'id' => $piatto['id'],
                    'nome' => $piatto['nome'],
                    'descrizione' => $piatto['descrizione']
                ];
            }, $piatti)
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
