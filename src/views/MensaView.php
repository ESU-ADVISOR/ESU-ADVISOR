<?php

namespace Views;

use Models\MenseModel;
use Models\UserModel;
use Views\Utils;

class MensaView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/mensa.html");
    }

    public function render(array $data = []): void
    {
        if (isset($data["nome"]) && !empty($data["nome"])) {

            $this->setTitle($data["nome"]." | ESU Advisor");

            $this->setDescription($data["nome"]." a Padova: Orari e informazioni. Scopri orari di apertura e indicazioni delle mense universitarie ESU.");

            $this->setKeywords($data["nome"].",menu,mensa,orari,ESU,Padova,università,informazioni,telefono,indirizzo,Google Maps");
        }

        
        $this->setBreadcrumb([
            'parent' => [
                'url' => 'index.php?mensa=' . urlencode($data["nome"]),
                'title' => 'Home'
            ],
            'current' => $data["nome"]
        ]);

        parent::render();

        $menseContent = "";
        $menseInfoContent = "";
        $piattiContent = "";
        $dishOfTheDayContent = "";

        foreach (MenseModel::findAll() as $datiMensa) {
            $selected = ($datiMensa->getNome() === $data["nome"]) ? 'selected' : '';
            $menseContent .= "<option value=\"" . htmlspecialchars($datiMensa->getNome()) . "\" " . $selected . ">" . htmlspecialchars($datiMensa->getNome()) . "</option>";
        }

        $mensaId = $data["nome"];

        $backToMensa = "<a href=\"./index.php?mensa=" . urlencode($mensaId) . "\" class=\"nav-button primary\" id=\"mensa-info-button\">Visualizza Piatti</a>";
        
        // === MENSE INFO ===
        $menseInfoContent .= "<h3 class=\"mense-info-name\">" . htmlspecialchars($data["nome"]) . "</h3>";

        $menseInfoContent .= "<dl class=\"contact-info\">";
        $menseInfoContent .= "<div class=\"contact-group\">";
        $menseInfoContent .= "<dt>Indirizzo:</dt>";
        $menseInfoContent .= "<dd>" . htmlspecialchars($data["indirizzo"]) . "</dd>";
        $menseInfoContent .= "</div>";

        $menseInfoContent .= "<div class=\"contact-group\">";
        $menseInfoContent .= "<dt>Telefono mensa:</dt>";
        $menseInfoContent .= "<dd><a href=\"tel:" . htmlspecialchars($data["telefono"]) . "\">" . htmlspecialchars($data["telefono"]) . "</a></dd>";
        $menseInfoContent .= "</div>";
        $menseInfoContent .= "</dl>";

        $menseInfoContent .= "<div class=\"schedule-container\">";
        $giorniSettimana = [
            "Lunedì",
            "Martedì",
            "Mercoledì",
            "Giovedì",
            "Venerdì",
            "Sabato",
            "Domenica",
        ];

        $menseInfoContent .= "
                <p class='sr-only' id='orari-mensa-description'>Tabella degli orari della mensa organizzata in due colonne: la prima indica i giorni della settimana, la seconda gli orari di apertura. Ogni riga corrisponde a un giorno.</p>
                <table aria-describedby=\"orari-mensa-description\">
                    <caption>Orari della mensa:</caption>
                    <thead>
                        <tr>
                            <th>Giorno</th>
                            <th>Orari</th>
                        </tr>
                    </thead>
                    <tbody>";

        $orari = $data["orari"] ?? null;
        foreach ($giorniSettimana as $giorno) {
            $menseInfoContent .= "<tr>
                        <th scope=\"row\" abbr=\"" . htmlspecialchars(substr($giorno, 0, 3)) . "\">" . htmlspecialchars($giorno) . "</th><td>";
            if ($orari) {
                $orariPerGiorno = [];
                foreach ($orari as $orario) {
                    if ($orario["Giorno"] === $giorno) {
                        $orariPerGiorno[] = "<time datetime=\"" . htmlspecialchars($orario["orainizio"]) . "\">" . htmlspecialchars($orario["orainizio"]) . "</time> - <time datetime=\"" . htmlspecialchars($orario["orafine"]) . "\">" . htmlspecialchars($orario["orafine"]) . "</time>";
                    }
                }
                if (!empty($orariPerGiorno)) {
                    $menseInfoContent .= implode("<br>", $orariPerGiorno);
                } else {
                    $menseInfoContent .= "Chiuso";
                }
            }
            $menseInfoContent .= "</td></tr>";
        }
        $menseInfoContent .= "</tbody></table>";

        $menseInfoContent .=
            "<a href=\"" . htmlspecialchars($data["maps_link"]) . "\" class=\"directions-button nav-button secondary text-center\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Direzioni su Google Maps per " . htmlspecialchars($data["nome"]) . " (si apre in una nuova finestra)\">
                        Direzioni su Google Maps
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"external-link-icon\" aria-hidden=\"true\" role=\"img\">
                            <title>Link esterno</title>
                            <path d=\"M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6\"></path>
                            <polyline points=\"15 3 21 3 21 9\"></polyline>
                            <line x1=\"10\" y1=\"14\" x2=\"21\" y2=\"3\"></line>
                        </svg>
                </a>";

        Utils::replaceTemplateContent(
            $this->dom,
            "mense-template",
            $menseContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-info-template",
            $menseInfoContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-piatti-template-link",
            $backToMensa
        );
        echo $this->dom->saveHTML();
    }
}
