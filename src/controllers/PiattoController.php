<?php

namespace Controllers;

use Models\PiattoModel;
use Views\PiattoView;
use Views\ErrorView;


class PiattoController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $piatto = null;

        $nome_piatto = $get["nome"] ?? null;
        $mensa = $get["mensa"] ?? null;

        foreach (PiattoModel::findAll() as $p) {
            if (
                str_replace(" ", "_", strtolower($p->getNome())) == $nome_piatto
            ) {
                $piatto = $p;
                break;
            }
        }

        if (isset($piatto) && !empty($piatto)) {
            $view = new PiattoView();
            $view->render([
                "nome" => $piatto->getNome(),
                "descrizione" => $piatto->getDescrizione(),
                "mensa" => $mensa,
            ]);
        } else {
            $view = new ErrorView();
            $view->render([
                "message" => "Il piatto richiesto non è disponibile.",
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
