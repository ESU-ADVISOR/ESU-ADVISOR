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
            $piattoDelGiorno = null;
            $bestAvg = 0;
            foreach ($piatti as $piatto) {
                if ($piatto->getAvgVote() > $bestAvg) {
                    $bestAvg = $piatto->getAvgVote();
                    $piattoDelGiorno = $piatto;
                }
            }

            $menus[] = [
                "nome" => $mensa->getNome(),
                "indirizzo" => $mensa->getIndirizzo(),
                "telefono" => $mensa->getTelefono(),
                "maps_link" => $mensa->getMapsLink(),
                "piatti" => $piatti,
                "piatto_del_giorno" => $piattoDelGiorno,
            ];
        }

        $view = new IndexView();

        $piatti = [];

        $view->render([
            "mense" => array_map(function ($menu) {
                return [
                    "nome" => $menu["nome"],
                    "indirizzo" => $menu["indirizzo"],
                    "telefono" => $menu["telefono"],
                    "maps_link" => $menu["maps_link"],
                    "piatti" => $menu["piatti"],
                    "piatto_del_giorno" => $menu["piatto_del_giorno"],
                ];
            }, $menus),
            "piatti" => array_map(function ($menu) {
                if (empty($menu["piatti"])) {
                    return null;
                }
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
            "error" => "Richiesta POST non consentita",
        ]);
        exit();
    }
}