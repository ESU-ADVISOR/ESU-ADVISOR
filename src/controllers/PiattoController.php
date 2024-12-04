<?php
namespace Controllers;

use Models\PiattoModel;
use Views\PiattoView;

class PiattoController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $nome_piatto = $get["nome"];

        $piatti = PiattoModel::findAll();

        $piatto = new PiattoModel();

        foreach ($piatti as $p) {
            if (
                str_replace(" ", "_", strtolower($p->getNome())) == $nome_piatto
            ) {
                $piatto = $p;
                break;
            }
        }

        $view = new PiattoView();
        $view->render([
            "nome" => $piatto->getNome(),
            "descrizione" => $piatto->getDescrizione(),
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
