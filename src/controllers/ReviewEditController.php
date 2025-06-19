<?php

namespace Controllers;

use Models\UserModel;
use Models\RecensioneModel;
use Models\MenuModel;
use Views\ReviewEditView;

class ReviewEditController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new ReviewEditView();

        if (!isset($get['piatto']) || !isset($get['mensa'])) {
            $view->renderError("Parametri mancanti", "Parametri piatto o mensa mancanti. Impossibile modificare la recensione.", 404);
            return;
        }

        if (!MenuModel::exists($get['piatto'], $get['mensa'])) {
            $view->renderError("Menu non trovato", "La combinazione piatto-mensa specificata non esiste.", 404);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if (!$user) {
            $view->renderError("Utente non trovato.", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $recensione = RecensioneModel::findByFields($user->getId(), $get['piatto'], $get['mensa']);

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

        if (!isset($post['piatto']) || !isset($post['mensa']) || !isset($post['action'])) {
            $view->renderError("Richiesta malformata", "Parametri mancanti.", 400);
            return;
        }

        if ($post['action'] === 'delete') {
            $this->handleDeleteRequest($post);
            return;
        }

        if (!isset($post['rating']) || !isset($post['review'])) {
            $view->renderError("Richiesta malformata", "Parametri mancanti. Per favore compila tutti i campi.", 400);
            return;
        }

        if (!MenuModel::exists($post['piatto'], $post['mensa'])) {
            $view->renderError("Menu non trovato", "La combinazione piatto-mensa specificata non esiste.", 404);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if (!$user) {
            $view->renderError("Utente non trovato.", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $recensione = RecensioneModel::findByFields($user->getId(), $post['piatto'], $post['mensa']);

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
                $recensione = RecensioneModel::findByFields($user->getId(), $post['piatto'], $post['mensa']);

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

    private function handleDeleteRequest(array $post): void
    {
        $view = new ReviewEditView();

        if (!MenuModel::exists($post['piatto'], $post['mensa'])) {
            $view->renderError("Menu non trovato", "La combinazione piatto-mensa specificata non esiste.", 404);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if (!$user) {
            $view->renderError("Utente non trovato.", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $recensione = RecensioneModel::findByFields($user->getId(), $post['piatto'], $post['mensa']);

        if (!$recensione) {
            $view->renderError("Recensione non trovata", "Recensione non trovata o non hai i permessi per eliminarla.", 404);
            return;
        }

        try {
            if ($recensione->deleteFromDB()) {
                header("Location: profile.php?success=" . urlencode("Recensione eliminata con successo!"));
                exit;
            } else {
                $view->render([
                    "errors" => ["Eliminazione recensione fallita: impossibile eliminare dal <span lang='en'>database</span>"],
                    "recensione" => $recensione
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore durante l'eliminazione della recensione: " . $e->getMessage());

            $view->render([
                "errors" => ["Eliminazione recensione fallita: " . $e->getMessage()],
                "recensione" => $recensione
            ]);
        }
    }
}
