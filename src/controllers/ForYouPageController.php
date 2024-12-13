<?php namespace Controllers;

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
        ]);

        try {
            if ($recensione->saveToDB()) {
                $view->render(["success" => "Review successfully submitted!"]);
                return;
            } else {
                $view->render([
                    "error" => "Review submission failed: ",
                    // "Review submission failed: " . $this->model->getLastError(),
                ]);
            }
        } catch (\Exception $e) {
            $view->render([
                "error" => "Review submission failed: " . $e->getMessage(),
            ]);
        }
    }
}
?>
