<?php

namespace Controllers;

use Models\RecensioneModel;
use Views\ForYouPageView;

class ForYouPageController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new ForYouPageView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new ForYouPageView();

        $recensione = new RecensioneModel([
            "voto" => $post["rating"],
            "descrizione" => $post["review"],
            "utente" => $_SESSION["email"],
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
