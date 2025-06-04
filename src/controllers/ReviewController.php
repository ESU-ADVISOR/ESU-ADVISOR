<?php

namespace Controllers;

use Models\UserModel;
use Models\RecensioneModel;
use Views\ReviewView;

class ReviewController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new ReviewView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new ReviewView();

        $user = UserModel::findByUsername($_SESSION["username"]);
        $idutente = $user->getId();
        $piatto = $post["piatto"];

        $recensione = new RecensioneModel([
            "voto" => $post["rating"],
            "descrizione" => $post["review"],
            "utente" => $user->getUsername(),
            "idutente" => $idutente,
            "piatto" => $piatto,
            "data" => date("Y-m-d H:i:s"),
        ]);

        try {
            if ($recensione->saveToDB()) {
                $view->render(["success" => "Recensione inviata con successo!"]);
                return;
            } else {
                $view->render([
                    "errors" => ["Invio recensione fallito: impossibile salvare nel <span lang='en'>database</span>"],
                    "formData" => $post
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore di recensione: " . $e->getMessage());
            $view->render([
                "errors" => ["Invio recensione fallito: " . $e->getMessage()],
                "formData" => $post
            ]);
        }
    }
}
