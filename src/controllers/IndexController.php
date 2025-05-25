<?php

namespace Controllers;

use Models\MenseModel;
use Models\PiattoModel;
use Models\PreferenzeUtenteModel;
use Views\IndexView;
use Views\ErrorView;

class IndexController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $mense = MenseModel::findAll();
        if(empty($mense)) {
            $view = new ErrorView();
            $view->render([
                    "message" => "Nessuna mensa disponibile.",
            ]);
            exit();
        }

        $mensaSelezionata = null;
        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);
        
        if(isset($get["mensa"]) && !empty($get["mensa"])) {
            $mensaSelezionata = MenseModel::findByName($get["mensa"]);
            if(!$mensaSelezionata) {
               $view = new ErrorView();
                $view->render([
                     "message" => "La mensa specificata non esiste.",
                ]);
                exit();
            }
        } else {
            $mensaPreferitaNome = null;
            
            if ($isLoggedIn) {
                $userPreferences = PreferenzeUtenteModel::findByUsername($_SESSION["username"]);
                if ($userPreferences) {
                    $mensaPreferitaNome = $userPreferences->getMensaPreferita();
                    if ($mensaPreferitaNome && !isset($_SESSION["mensa_preferita"])) {
                        $_SESSION["mensa_preferita"] = $mensaPreferitaNome;
                    }
                }
            }
            
            if (!$mensaPreferitaNome && isset($_SESSION["mensa_preferita"]) && !empty($_SESSION["mensa_preferita"])) {
                $mensaPreferitaNome = $_SESSION["mensa_preferita"];
            }
            
            if ($mensaPreferitaNome) {
                $mensaSelezionata = MenseModel::findByName($mensaPreferitaNome);
            }
            
            if (!$mensaSelezionata) {
                $mensaSelezionata = $mense[0];
            }
        }

        $piatti = $mensaSelezionata->getPiatti();
        $piattoDelGiorno = null;
        $bestAvg = 0;
        foreach ($piatti as $piatto) {
            if ($piatto->getAvgVote() > $bestAvg) {
                $bestAvg = $piatto->getAvgVote();
                $piattoDelGiorno = $piatto;
            }
        }

        $datiMensa[] = [
            "nome" => $mensaSelezionata->getNome(),
            "indirizzo" => $mensaSelezionata->getIndirizzo(),
            "telefono" => $mensaSelezionata->getTelefono(),
            "maps_link" => $mensaSelezionata->getMapsLink(),
            "orari" => $mensaSelezionata->getMenseOrari(),
        ];

        $nomiMense = [];
        foreach ($mense as $mensa) {
            $nomiMense[] = $mensa->getNome();
        }

        $view = new IndexView();
        $view->render([
            "mense" => $nomiMense,
            "mensa_selezionata" => $datiMensa,
            "piatti" => $piatti,
            "piatto_del_giorno" => $piattoDelGiorno,
        ]);
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