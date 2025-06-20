<?php

namespace Controllers;

use Models\MenseModel;
use Models\MenuModel;
use Models\PiattoModel;
use Models\PreferenzeUtenteModel;
use Views\IndexView;
use Views\ErrorView;

class IndexController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $mense = MenseModel::findAll();
        if (empty($mense)) {
            $view = new ErrorView();
            $view->render([
                "message" => "Nessuna mensa disponibile.",
            ]);
            exit();
        }

        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        $mensaInizialeNome = null;
        if (isset($get["mensa"]) && !empty($get["mensa"])) {
            $mensaTest = MenseModel::findByName($get["mensa"]);
            if ($mensaTest) {
                $mensaInizialeNome = $get["mensa"];
            }
        }

        if (!$mensaInizialeNome && $isLoggedIn) {
            $userPreferences = PreferenzeUtenteModel::findByUsername($_SESSION["username"]);
            if ($userPreferences) {
                $mensaPreferitaNome = $userPreferences->getMensaPreferita();
                if ($mensaPreferitaNome && !isset($_SESSION["mensa_preferita"])) {
                    $_SESSION["mensa_preferita"] = $mensaPreferitaNome;
                }
                $mensaInizialeNome = $mensaPreferitaNome;
            }
        }

        if (!$mensaInizialeNome && isset($_SESSION["mensa_preferita"]) && !empty($_SESSION["mensa_preferita"])) {
            $mensaInizialeNome = $_SESSION["mensa_preferita"];
        }

        if (!$mensaInizialeNome) {
            $mensaInizialeNome = $mense[0]->getNome();
        }

        $mensa = MenseModel::findByName($mensaInizialeNome);
        if ($mensa != null) {
            $piatti = $mensa->getPiatti();
            $piattoDelGiorno = null;
            $bestAvg = 0;

            foreach ($piatti as $piatto) {
                if ($piatto->getAvgVote() > $bestAvg) {
                    $bestAvg = $piatto->getAvgVote();
                    $piattoDelGiorno = $piatto;
                }
            }

            $datiCompleti[] = [
                "nome" => $mensa->getNome(),
                "indirizzo" => $mensa->getIndirizzo(),
                "telefono" => $mensa->getTelefono(),
                "maps_link" => $mensa->getMapsLink(),
                "orari" => $mensa->getMenseOrari(),
                "piatti" => $piatti,
                "piatto_del_giorno" => $piattoDelGiorno,
            ];

            $view = new IndexView();
            $view->render([
                "mense_complete" => $datiCompleti,
                "mensa_iniziale" => $mensaInizialeNome,
            ]);
        } else {
            $view = new ErrorView();
            $view->render([
                "message" => "Nessuna mensa disponibile.",
            ]);
            exit();
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
