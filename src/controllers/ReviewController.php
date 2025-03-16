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

        $recensione = new RecensioneModel([
            "voto" => $post["rating"],
            "descrizione" => $post["review"],
            "utente" => UserModel::findByUsername($_SESSION["username"])->getEmail(),
            "piatto" => $post["piatto"],
            "data" => date("Y-m-d H:i:s"),
        ]);

        try {
            if ($recensione->saveToDB()) {
                $view->render(["success" => "Review successfully submitted!"]);
                return;
            } else {
                $view->render([
                    "errors" => ["Review submission failed: could not save to database"],
                ]);
            }
        } catch (\Exception $e) {
            $view->render([
                "errors" => ["Review submission failed: " . $e->getMessage()],
            ]);
        }
    }
}
