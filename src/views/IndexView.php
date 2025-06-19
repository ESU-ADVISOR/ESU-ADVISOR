<?php

namespace Views;

use Models\MenseModel;
use Models\UserModel;
use Views\Utils;

class IndexView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/index.html");
    }

    public function render(array $data = []): void
    {
        // Personalizza SEO per la mensa iniziale
        if (isset($data["mensa_iniziale"]) && !empty($data["mensa_iniziale"])) {
            $mensaIniziale = $data["mensa_iniziale"];

            // Title dinamico con nome della mensa
            $this->setTitle("Menu $mensaIniziale - Mense Universitarie Padova | ESU Advisor");

            // Description specifica per la mensa
            $this->setDescription("Consulta il menu di oggi di $mensaIniziale a Padova. Scopri piatti, orari, recensioni e allergeni della mensa universitaria ESU.");

            // Keywords specifiche per la mensa
            $this->setKeywords("$mensaIniziale,menu,orari,ESU Padova,mense universitarie,piatti,recensioni,allergeni,piatto del giorno");
        }

        parent::render();

        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );

        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );

        $menseContent = "";
        $menseInfoContent = "";
        $piattiContent = "";
        $dishOfTheDayContent = "";

        $menseComplete = $data["mense_complete"];
        $mensaIniziale = $data["mensa_iniziale"];

        // Genera il select delle mense
        foreach (MenseModel::findAll() as $datiMensa) {
            $selected = ($datiMensa->getNome() === $mensaIniziale) ? 'selected' : '';
            $menseContent .= "<option value=\"" . htmlspecialchars($datiMensa->getNome()) . "\" " . $selected . ">" . htmlspecialchars($datiMensa->getNome()) . "</option>";
        }

        // Genera il contenuto della mensa selezionata
        $datiMensa = $menseComplete[0]; // Ora l'array contiene solo la mensa selezionata
        $mensaId = $datiMensa["nome"];

        // === MENSE INFO ===
        $menseInfoContent .= "<h3 class=\"mense-info-name\">" . htmlspecialchars($datiMensa["nome"]) . "</h3>";

        $menseInfoContent .= "<dl class=\"contact-info\">";
        $menseInfoContent .= "<div class=\"contact-group\">";
        $menseInfoContent .= "<dt>Indirizzo:</dt>";
        $menseInfoContent .= "<dd>" . htmlspecialchars($datiMensa["indirizzo"]) . "</dd>";
        $menseInfoContent .= "</div>";

        $menseInfoContent .= "<div class=\"contact-group\">";
        $menseInfoContent .= "<dt>Telefono mensa:</dt>";
        $menseInfoContent .= "<dd>" . htmlspecialchars($datiMensa["telefono"]) . "</dd>";
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
                <p class='sr-only' id='orari-mensa-description-" . htmlspecialchars(str_replace(' ', '-', strtolower($mensaId))) . "'>Tabella degli orari della mensa organizzata in due colonne: la prima indica i giorni della settimana, la seconda gli orari di apertura. Ogni riga corrisponde a un giorno.</p>
                <table aria-describedby=\"orari-mensa-description-" . htmlspecialchars(str_replace(' ', '-', strtolower($mensaId))) . "\">
                    <caption>Orari della mensa:</caption>
                    <thead>
                        <tr>
                            <th scope=\"col\">Giorno</th>
                            <th scope=\"col\">Orari</th>
                        </tr>
                    </thead>
                    <tbody>";

        $orari = $datiMensa["orari"] ?? null;
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
            "<a href=\"" . htmlspecialchars($datiMensa["maps_link"]) . "\" class=\"directions-button nav-button secondary text-center\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Direzioni su Google Maps per " . htmlspecialchars($datiMensa["nome"]) . " (si apre in una nuova finestra)\">
                        Direzioni su Google Maps
                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"external-link-icon\" aria-hidden=\"true\" role=\"img\">
                            <title>Link esterno</title>
                            <path d=\"M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6\"></path>
                            <polyline points=\"15 3 21 3 21 9\"></polyline>
                            <line x1=\"10\" y1=\"14\" x2=\"21\" y2=\"3\"></line>
                        </svg>
                </a>";

        // === PIATTI DEL MENU ===
        if (isset($datiMensa["piatti"]) && is_array($datiMensa["piatti"]) && !empty($datiMensa["piatti"])) {
            foreach ($datiMensa["piatti"] as $piatto) {
                $piattiContent .= "<li class=\"menu-item\">";
                $piattiContent .= "<article>";

                if ($piatto->getImage()) {
                    $piattiContent .=
                        "<figure aria-labelledby=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "-" . htmlspecialchars(str_replace(" ", "-", strtolower($mensaId))) . "\">
                                <img src=\"" . $piatto->getImage() . "\" alt=\"" .
                        htmlspecialchars($piatto->getNome()) .
                        "\" width=\"150\" height=\"150\" >
                                <figcaption id=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "-" . htmlspecialchars(str_replace(" ", "-", strtolower($mensaId))) . "\" class=\"sr-only\">
                                    Immagine del piatto: " . htmlspecialchars($piatto->getNome()) . "
                                </figcaption>
                            </figure>";
                } else {
                    $piattiContent .=
                        "<figure aria-labelledby=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "-" . htmlspecialchars(str_replace(" ", "-", strtolower($mensaId))) . "\">
                                <img src=\"images/placeholder.png\" alt=\"" .
                        htmlspecialchars($piatto->getNome()) .
                        "\" width=\"150\" height=\"150\" >
                                <figcaption id=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "-" . htmlspecialchars(str_replace(" ", "-", strtolower($mensaId))) . "\" class=\"sr-only\">
                                    Immagine di anteprima per: " . htmlspecialchars($piatto->getNome()) . "
                                </figcaption>
                            </figure>";
                }

                $piattiContent .= "<div class=\"menu-item-content\">";

                //Check for allergens
                $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];

                $piattiContent .= "<h3>" . htmlspecialchars($piatto->getNome()) . "</h3>";
                $piattiContent .= "<p>" . htmlspecialchars($piatto->getDescrizione()) . "</p>";

                $allergeni = $piatto->getAllergeni();
                if (!empty($allergeni)) {
                    $allergeniFormatted = [];
                    foreach ($allergeni as $allergene) {
                        if (in_array($allergene, $userAllergeni)) {
                            $allergeniFormatted[] = "<span class=\"allergen-highlighted\">" . htmlspecialchars($allergene) . "</span>";
                        } else {
                            $allergeniFormatted[] = htmlspecialchars($allergene);
                        }
                    }
                    $piattiContent .= "<div class=\"allergens-list\">
                            <p><strong>Allergeni:</strong> " . implode(", ", $allergeniFormatted) . "</p>
                        </div>";
                }

                $ratingValue = $piatto->getAvgVote();
                $ratingText = sprintf("Valutazione: %.1f su 5", $ratingValue);
                $piattiContent .= "<div class=\"ratings\" aria-label=\"" . htmlspecialchars($ratingText) . "\" role=\"img\">";
                $piattiContent .= "<span class=\"sr-only\">" . htmlspecialchars($ratingText) . "</span>";

                for ($i = 0; $i < $ratingValue; $i++) {
                    $piattiContent .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $ratingValue; $i++) {
                    $piattiContent .= $starSVG;
                }
                $piattiContent .= "</div>";

                $piattiContent .=
                    "<a href=\"./piatto.php?nome=" .
                    urldecode(str_replace(" ", "_", strtolower($piatto->getNome()))) .
                    "&mensa=" . urlencode($mensaId) .
                    "\">Vedi recensioni</a>" .
                    "</div>" .
                    "</article>" .
                    "</li>";
            }
        } else {
            $piattiContent .= "<li class=\"empty-menu\"><p class=\"text-center\">Nessun piatto disponibile per questa mensa</p></li>";
        }

        // === PIATTO DEL GIORNO ===
        if (isset($datiMensa["piatto_del_giorno"]) && $datiMensa["piatto_del_giorno"]) {
            $piatto = $datiMensa["piatto_del_giorno"];
            $dishOfTheDayContent .= "<div class=\"menu-item dish-of-day-item\">";
            $dishOfTheDayContent .= "<article>";

            $dishId = htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome())) . "-day-" . str_replace(" ", "-", strtolower($mensaId)));
            if ($piatto->getImage()) {
                $dishOfTheDayContent .=
                    "<figure aria-labelledby=\"caption-" . $dishId . "\">
                            <img src=\"" . $piatto->getImage() . "\" alt=\"" .
                    htmlspecialchars($piatto->getNome()) .
                    "\" width=\"150\" height=\"150\" >
                            <figcaption id=\"caption-" . $dishId . "\" class=\"sr-only\">
                                Piatto del giorno: " . htmlspecialchars($piatto->getNome()) . "
                            </figcaption>
                        </figure>";
            } else {
                $dishOfTheDayContent .=
                    "<figure aria-labelledby=\"caption-" . $dishId . "\">
                            <img src=\"images/placeholder.png\" alt=\"" .
                    htmlspecialchars($piatto->getNome()) .
                    "\" width=\"150\" height=\"150\" >
                            <figcaption id=\"caption-" . $dishId . "\" class=\"sr-only\">
                                Piatto del giorno (immagine di anteprima): " . htmlspecialchars($piatto->getNome()) . "
                            </figcaption>
                        </figure>";
            }

            $dishOfTheDayContent .= "<div class=\"menu-item-content\">";

            //Check for allergens
            $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];

            $dishOfTheDayContent .= "<h3>" . htmlspecialchars($piatto->getNome()) . "</h3>";
            $dishOfTheDayContent .= "<p>" . htmlspecialchars($piatto->getDescrizione()) . "</p>";

            $allergeni = $piatto->getAllergeni();
            if (!empty($allergeni)) {
                $allergeniFormatted = [];
                foreach ($allergeni as $allergene) {
                    if (in_array($allergene, $userAllergeni)) {
                        $allergeniFormatted[] = "<span class=\"allergen-highlighted\">" . htmlspecialchars($allergene) . "</span>";
                    } else {
                        $allergeniFormatted[] = htmlspecialchars($allergene);
                    }
                }
                $dishOfTheDayContent .= "<div class=\"allergens-list\">
                        <p><strong>Allergeni:</strong> " . implode(", ", $allergeniFormatted) . "</p>
                    </div>";
            }

            $ratingValue = $piatto->getAvgVote();
            $ratingText = sprintf("Valutazione del piatto del giorno: %.1f su 5", $ratingValue);
            $dishOfTheDayContent .= "<div class=\"ratings\" aria-label=\"" . htmlspecialchars($ratingText) . "\" role=\"img\">";
            $dishOfTheDayContent .= "<span class=\"sr-only\">" . htmlspecialchars($ratingText) . "</span>";

            for ($i = 0; $i < $ratingValue; $i++) {
                $dishOfTheDayContent .= $starFilledSVG;
            }
            for ($i = 0; $i < 5 - $ratingValue; $i++) {
                $dishOfTheDayContent .= $starSVG;
            }
            $dishOfTheDayContent .= "</div>";

            // Link pulito senza parametro mensa
            $dishOfTheDayContent .=
                "<a href=\"./piatto.php?nome=" .
                urldecode(str_replace(" ", "_", strtolower($piatto->getNome()))) .
                "&mensa=" . urlencode($mensaId) .
                "\">Vedi <span lang=\"en\">recensioni</span></a>" .
                "</div>" .
                "</article>";

            $dishOfTheDayContent .= "</div>";
        } else {
            $dishOfTheDayContent .= "<p class=\"text-center text-secondary dish-of-day-empty\">Nessun piatto del giorno disponibile per questa mensa</p>";
        }

        // Replace template placeholders with actual content
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-template",
            $menseContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "piatti-template",
            $piattiContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "dish-of-the-day-template",
            $dishOfTheDayContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-info-template",
            $menseInfoContent
        );

        echo $this->dom->saveHTML();
    }
}
