<?php
namespace Controllers;

use Models\MenseModel;
use Models\PiattoModel;
use Views\IndexView;

class IndexController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $menus = [];
        foreach (MenseModel::findAll() as $mensa) {
            $menuModel = $mensa->getCurrentMenu();
            $piatti = $menuModel->getPiatti();

            $menus[] = [
                "nome" => $mensa->getNome(),
                "indirizzo" => $mensa->getIndirizzo(),
                "piatti" => $piatti,
            ];
        }

        $view = new IndexView();

        $piatti = [];

        $view->render([
            "mense" => array_map(function ($menu) {
                return [
                    "nome" => $menu["nome"],
                    "indirizzo" => $menu["indirizzo"],
                    "piatti" => $menu["piatti"],
                ];
            }, $menus),
            //
            // "menu" => array_map(function (MenuModel $menu) {
            //     return [
            //         "data" => $menu->getData(),
            //         "menu" => $menu->get(),
            //     ];
            // }, $menu),
            "piatti" => array_map(function ($menu) {
                return array_map(function (PiattoModel $piatto) {
                    return [
                        "nome" => $piatto->getNome(),
                        "descrizione" => $piatto->getDescrizione(),
                    ];
                }, $menu["piatti"])[0];
            }, $menus),
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
