<?php
namespace Controllers;

use Models\PiattoModel;
use Views\PiattoView;
use Views\ErrorView;


class PiattoController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $nome_piatto = $get["nome"];

        $piatto = PiattoModel::findByName($get["nome"]);

        if(!empty($piatto)) {
            $view = new PiattoView();
            $view->render([
                "nome" => $piatto->getNome(),
                "descrizione" => $piatto->getDescrizione(),
            ]);
        }else{
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