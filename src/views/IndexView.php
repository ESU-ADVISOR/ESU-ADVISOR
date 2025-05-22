<?php

namespace Views;

use Models\MenseModel;
use Models\UserModel;
use Models\PreferenzeUtenteModel;
use Views\Utils;

class IndexView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/index.html");
    }

    public function render(array $data = []): void
    {
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

        if (isset($data["mense"]) && is_array($data["mense"])) {
            $id = 0;
            $mensaPreferita = null;
            if(isset($_SESSION["username"])){
                $preferences = PreferenzeUtenteModel::findByUsername($_SESSION["username"]);
                $mensaPreferita = $preferences->getMensa()->getNome();
            }else if(isset($_SESSION["mensa_preferita"])){
                $mensaPreferita = $_SESSION["mensa_preferita"];
            }
            foreach ($data["mense"] as $mensa) {
                if(isset($mensaPreferita) && $mensaPreferita == $mensa["nome"]){
                    $menseContent .= "<option value=\"" . htmlspecialchars($mensa["nome"]) . "\" selected>" . htmlspecialchars($mensa["nome"]) . "</option>";
                }else{
                    $menseContent .=
                        "<option value=\"" .
                        htmlspecialchars($id) .
                        "\">" .
                        htmlspecialchars($mensa["nome"]) .
                        "</option>";
                }
                $menseInfoContent .= "<li class=\"mense-info-item\" data-mensa-id=\"" .
                    htmlspecialchars($id) . "\">";

                $menseInfoContent .= "<h3>" . htmlspecialchars($mensa["nome"]) . "</h3>";

                $menseInfoContent .= "<dl class=\"contact-info\">";
                $menseInfoContent .= "<div class=\"contact-group\">";
                $menseInfoContent .= "<dt>Indirizzo:</dt>";
                $menseInfoContent .= "<dd>" . htmlspecialchars($mensa["indirizzo"]) . "</dd>";
                $menseInfoContent .= "</div>";

                $menseInfoContent .= "<div class=\"contact-group\">";
                $menseInfoContent .= "<dt>Telefono mensa:</dt>";
                $menseInfoContent .= "<dd>" . htmlspecialchars($mensa["telefono"]) . "</dd>";
                $menseInfoContent .= "</div>";
                $menseInfoContent .= "</dl>";

                $menseInfoContent .= "<div class=\"schedule-container\">";
                $orari = MenseModel::findByName(
                    $mensa["nome"]
                )->getMenseOrari();

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
                        <p id='orari-mensa-description'>Tabella degli orari della mensa organizzata in due colonne: la prima indica i giorni della settimana, la seconda gli orari di apertura. Ogni riga corrisponde a un giorno.</p>
                        <table aria-describedby=\"orari-mensa-description\">
                            <caption id=\"orari-mensa-caption\">Orari:</caption>
                            <thead>
                                <tr>
                                    <th scope=\"col\">Giorno</th>
                                    <th scope=\"col\">Orari</th>
                                </tr>
                            </thead>
                            <tbody>";

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
                            $menseInfoContent .= implode(", ", $orariPerGiorno);
                        } else {
                            $menseInfoContent .= "Chiuso";
                        }
                    }
                    $menseInfoContent .= "</td></tr>";
                }
                $menseInfoContent .= "</tbody></table>";

                // Add maps link
                $menseInfoContent .=
                    "<a href=\"" .
                    htmlspecialchars($mensa["maps_link"]) .
                    "\" class=\"directions-button nav-button secondary text-center\" target=\"_blank\" rel=\"noopener noreferrer\" aria-label=\"Direzioni su Google Maps per " . htmlspecialchars($mensa["nome"]) . " (si apre in una nuova finestra)\">
                            Direzioni su Google Maps
                            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"external-link-icon\" aria-hidden=\"true\" role=\"img\" aria-labelledby=\"external-link-title-" . $id . "\">
                                <title id=\"external-link-title-" . $id . "\">Link esterno</title>
                                <path d=\"M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6\"></path>
                                <polyline points=\"15 3 21 3 21 9\"></polyline>
                                <line x1=\"10\" y1=\"14\" x2=\"21\" y2=\"3\"></line>
                            </svg>
                    </a>";

                $menseInfoContent .= "</li>";

                // =========== Corrected Logic for Menu Items ============
                if (isset($mensa["piatti"]) && is_array($mensa["piatti"]) && !empty($mensa["piatti"])) {
                    $piattiForThisMensa = "";
                    foreach ($mensa["piatti"] as $piatto) {
                        $piattiForThisMensa .= "<article class=\"menu-item\">";
                        if ($piatto->getImage()) {
                            $piattiForThisMensa .=
                                "<figure aria-labelledby=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "\">
                                    <img src=\"" . $piatto->getImage() . "\" alt=\"" .
                                htmlspecialchars($piatto->getNome()) .
                                "\" width=\"150\" height=\"150\" >
                                    <figcaption id=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "\" class=\"sr-only\">
                                        Immagine del piatto: " . htmlspecialchars($piatto->getNome()) . "
                                    </figcaption>
                                </figure>";
                        } else {
                            $piattiForThisMensa .=
                                "<figure aria-labelledby=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "\">
                                    <img src=\"images/placeholder.png\" alt=\"" .
                                htmlspecialchars($piatto->getNome()) .
                                "\" width=\"150\" height=\"150\" >
                                    <figcaption id=\"caption-" . htmlspecialchars(str_replace(" ", "-", strtolower($piatto->getNome()))) . "\" class=\"sr-only\">
                                        Immagine di anteprima per: " . htmlspecialchars($piatto->getNome()) . "
                                    </figcaption>
                                </figure>";
                        }
                        $piattiForThisMensa .=
                            "<div class=\"menu-item-content\">";

                        // Check for allergens
                        $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];
                        $hasAllergens = $piatto->containsAllergens($userAllergeni);

                        if ($hasAllergens) {
                            $piattiForThisMensa .= "<div class=\"allergen-warning\" role=\"alert\">
                                <strong>Attenzione:</strong> Questo piatto contiene allergeni da te segnalati.
                            </div>";
                        }

                        $piattiForThisMensa .= "<h3>" .
                            htmlspecialchars($piatto->getNome()) .
                            "</h3>";
                        $piattiForThisMensa .=
                            "<p>" .
                            htmlspecialchars($piatto->getDescrizione()) .
                            "</p>";

                        $allergeni = $piatto->getAllergeni();
                        if (!empty($allergeni)) {
                            $piattiForThisMensa .= "<div class=\"allergens-list\">
                                <p><strong>Allergeni:</strong> " . htmlspecialchars(implode(", ", $allergeni)) . "</p>
                            </div>";
                        }

                        $ratingValue = $piatto->getAvgVote();
                        $ratingText = sprintf("Valutazione: %.1f su 5", $ratingValue);
                        $piattiForThisMensa .= "<div class=\"ratings\" aria-label=\"" . htmlspecialchars($ratingText) . "\" role=\"img\">";

                        $piattiForThisMensa .= "<span class=\"sr-only\">" . htmlspecialchars($ratingText) . "</span>";

                        for ($i = 0; $i < $ratingValue; $i++) {
                            $piattiForThisMensa .= $starFilledSVG;
                        }
                        for ($i = 0; $i < 5 - $ratingValue; $i++) {
                            $piattiForThisMensa .= $starSVG;
                        }
                        $piattiForThisMensa .= "</div>";
                        $piattiForThisMensa .=
                            "<a href=\"./piatto.php?nome=" .
                            htmlspecialchars(
                                str_replace(
                                    " ",
                                    "_",
                                    strtolower($piatto->getNome())
                                )
                            ) .
                            "\">Vedi recensioni</a>" .
                            "</div>" .
                            "</article>";
                    }

                    $piattiContent .= "<li class=\"menu-item-container\" data-mensa-id=\"" .
                        htmlspecialchars($id) . "\">" . $piattiForThisMensa . "</li>";
                } else {
                    $piattiContent .= "<li class=\"menu-item-container\" data-mensa-id=\"" .
                        htmlspecialchars($id) .
                        "\"><div class=\"empty-menu\"><p class=\"text-center\">Nessun piatto disponibile per questa mensa</p></div></li>";
                }

                // =========== Corrected Logic for Dish of the Day ============
                if (isset($mensa["piatto_del_giorno"]) && $mensa["piatto_del_giorno"]) {
                    $dishOfTheDayContent .= "<div class=\"menu-item-container\" data-mensa-id=\"" .
                        htmlspecialchars($id) . "\">";

                    $dishOfTheDayContent .= "<article class=\"menu-item\">";

                    $dishId = htmlspecialchars(str_replace(" ", "-", strtolower($mensa["piatto_del_giorno"]->getNome())) . "-day");
                    if ($mensa["piatto_del_giorno"]->getImage()) {
                        $dishOfTheDayContent .=
                            "<figure aria-labelledby=\"caption-" . $dishId . "\">
                                <img src=\"" . $mensa["piatto_del_giorno"]->getImage() . "\" alt=\"" .
                            htmlspecialchars($mensa["piatto_del_giorno"]->getNome()) .
                            "\" width=\"150\" height=\"150\" >
                                <figcaption id=\"caption-" . $dishId . "\" class=\"sr-only\">
                                    Piatto del giorno: " . htmlspecialchars($mensa["piatto_del_giorno"]->getNome()) . "
                                </figcaption>
                            </figure>";
                    } else {
                        $dishOfTheDayContent .=
                            "<figure aria-labelledby=\"caption-" . $dishId . "\">
                                <img src=\"images/placeholder.png\" alt=\"" .
                            htmlspecialchars($mensa["piatto_del_giorno"]->getNome()) .
                            "\" width=\"150\" height=\"150\" >
                                <figcaption id=\"caption-" . $dishId . "\" class=\"sr-only\">
                                    Piatto del giorno (immagine di anteprima): " . htmlspecialchars($mensa["piatto_del_giorno"]->getNome()) . "
                                </figcaption>
                            </figure>";
                    }

                    $dishOfTheDayContent .= "<div class=\"menu-item-content\">";

                    $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];
                    $hasAllergens = $mensa["piatto_del_giorno"]->containsAllergens($userAllergeni);

                    if ($hasAllergens) {
                        $dishOfTheDayContent .= "<div class=\"allergen-warning\" role=\"alert\">
                            <strong>Attenzione:</strong> Questo piatto contiene allergeni da te segnalati.
                        </div>";
                    }

                    $dishOfTheDayContent .=
                        "<h3>" .
                        htmlspecialchars(
                            $mensa["piatto_del_giorno"]->getNome()
                        ) .
                        "</h3>";
                    $dishOfTheDayContent .=
                        "<p>" .
                        htmlspecialchars(
                            $mensa["piatto_del_giorno"]->getDescrizione()
                        ) .
                        "</p>";

                    $allergeni = $mensa["piatto_del_giorno"]->getAllergeni();
                    if (!empty($allergeni)) {
                        $dishOfTheDayContent .= "<div class=\"allergens-list\">
                            <p><strong>Allergeni:</strong> " . htmlspecialchars(implode(", ", $allergeni)) . "</p>
                        </div>";
                    }

                    $ratingValue = $mensa["piatto_del_giorno"]->getAvgVote();
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
                    $dishOfTheDayContent .=
                        "<a href=\"./piatto.php?nome=" .
                        htmlspecialchars(
                            str_replace(
                                " ",
                                "_",
                                strtolower(
                                    $mensa["piatto_del_giorno"]->getNome()
                                )
                            )
                        ) .
                        "\">Vedi <span lang=\"en\">reviews</span></a>" .
                        "</div>" .
                        "</article>";

                    $dishOfTheDayContent .= "</div>";
                } else {
                    $dishOfTheDayContent .= "<div class=\"menu-item-container\" data-mensa-id=\"" .
                        htmlspecialchars($id) .
                        "\"><div class=\"empty-dish\"><p class=\"text-center\">Nessun piatto del giorno disponibile</p></div></div>";
                }

                $id++;
            }
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
        // Save the HTML from DOMDocument
        $html = $this->dom->saveHTML();

        // Output the HTML
        echo $html;
    }
}
