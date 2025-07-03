<?php

namespace Controllers;

use Models\MenseModel;
use Views\MensaView;
use Views\ErrorView;


class MensaController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $mense = MenseModel::findAll();
        $tmp = $get["mensa"] ?? null;
        $mensa = null;

        foreach ($mense as $p) {
            if (
                (urldecode($p->getNome())) == $tmp
            ) {
                $mensa = $p;
                break;
            }
        }

        if (isset($mensa) && !empty($mensa)) {
            $view = new MensaView();
            $view->render([
                "nome" => $mensa->getNome(),
                "indirizzo" => $mensa->getIndirizzo(),
                "telefono" => $mensa->getTelefono(),
                "orari" => $mensa->getMenseOrari(),
                "maps_link" => $mensa->getMapsLink(),
            ]);
        } else {
            $view = new ErrorView();
            $view->render([
                "message" => "La mensa richiesta non esiste.",
            ]);
        }
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
