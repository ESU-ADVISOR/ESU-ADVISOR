<?php

namespace Controllers;

use Models\UserModel;
use Models\RecensioneModel;
use Views\ReviewEditView;

class ReviewEditController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new ReviewEditView();

        if (!isset($get['piatto'])) {
            $view->renderError("Piatto non trovato", "Parametro piatto mancante. Impossibile modificare la recensione.", 404);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if (!$user) {
            $view->renderError("Utente non trovato.", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $recensione = RecensioneModel::findByFields($user->getUsername(), $get['piatto']);

        if (!$recensione) {
            $view->renderError("Recensione non trovata", "Recensione non trovata o non hai i permessi per modificarla.", 404);
            return;
        }

        $view->render([
            'recensione' => $recensione
        ]);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new ReviewEditView();

        if (!isset($post['piatto']) || !isset($post['rating']) || !isset($post['review'])) {
            $view->renderError("Richiesta malformata", "Parametri mancanti. Per favore compila tutti i campi.", 400);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if (!$user) {
            $view->renderError("Utente non trovato.", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $recensione = RecensioneModel::findByFields($user->getUsername(), $post['piatto']);

        if (!$recensione) {
            $view->renderError("Recensione non trovata", "Recensione non trovata o non hai i permessi per modificarla.", 404);
            return;
        }

        $recensione->setVoto($post['rating']);
        $recensione->setDescrizione($post['review']);
        $recensione->setData(date("Y-m-d H:i:s"));
        $recensione->setEdited(true);

        try {
            if ($recensione->saveToDB()) {
                $recensione = RecensioneModel::findByFields($user->getUsername(), $post['piatto']);

                $view->render([
                    "success" => "Recensione aggiornata con successo!",
                    "recensione" => $recensione
                ]);
                return;
            } else {
                $view->render([
                    "errors" => ["Aggiornamento recensione fallito: impossibile salvare nel <span lang='en'>database</span>"],
                    "recensione" => $recensione
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore durante l'aggiornamento della recensione: " . $e->getMessage());

            $view->render([
                "errors" => ["Aggiornamento recensione fallito: " . $e->getMessage()],
                "recensione" => $recensione
            ]);
        }
    }
}
