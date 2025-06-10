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
            $this->setKeywords("$mensaIniziale, menu $mensaIniziale, mensa $mensaIniziale, orari $mensaIniziale, ESU Padova, mense universitarie padova");
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
        foreach ($menseComplete as $datiMensa) {
            $selected = ($datiMensa["nome"] === $mensaIniziale) ? 'selected' : '';
            $menseContent .= "<option value=\"" . htmlspecialchars($datiMensa["nome"]) . "\" " . $selected . ">" . htmlspecialchars($datiMensa["nome"]) . "</option>";
        }

        // Genera il contenuto di TUTTE le mense (ma mostra solo quella iniziale)
        foreach ($menseComplete as $datiMensa) {
            $mensaId = $datiMensa["nome"];
            $isInitialMensa = ($mensaId === $mensaIniziale);
            $displayStyle = $isInitialMensa ? '' : ' style="display: none;"';

            // === MENSE INFO ===
            $menseInfoContent .= "<li class=\"mense-info-item\" data-mensa-id=\"" . htmlspecialchars($mensaId) . "\"" . $displayStyle . ">";
            $menseInfoContent .= "<h3>" . htmlspecialchars($datiMensa["nome"]) . "</h3>";

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
                <p id='orari-mensa-description-" . htmlspecialchars(str_replace(' ', '-', strtolower($mensaId))) . "'>Tabella degli orari della mensa organizzata in due colonne: la prima indica i giorni della settimana, la seconda gli orari di apertura.</p>
                <table class=\"schedule-table\" aria-describedby='orari-mensa-description-" . htmlspecialchars(str_replace(' ', '-', strtolower($mensaId))) . "'>
                <caption class=\"schedule-caption\">Orari di apertura mensa " . htmlspecialchars($datiMensa["nome"]) . "</caption>
                <thead>
                    <tr>
                        <th scope=\"col\">Giorno</th>
                        <th scope=\"col\">Orari</th>
                    </tr>
                </thead>
                <tbody>";

            foreach ($giorniSettimana as $giorno) {
                $orario = isset($datiMensa["orari"][$giorno])
                    ? htmlspecialchars($datiMensa["orari"][$giorno])
                    : "Chiuso";
                $menseInfoContent .= "<tr><td>" . htmlspecialchars($giorno) . "</td><td>" . $orario . "</td></tr>";
            }

            $menseInfoContent .= "</tbody></table></div>";

            if (!empty($datiMensa["maps_link"])) {
                $menseInfoContent .= "<a href=\"" . htmlspecialchars($datiMensa["maps_link"]) . "\" class=\"btn btn-primary\" target=\"_blank\" rel=\"noopener noreferrer\">Visualizza su Google Maps</a>";
            }

            $menseInfoContent .= "</li>";

            // === PIATTI ===
            $piatti = $datiMensa["piatti"];
            $piattiContent .= "<ul class=\"dishes-list\" data-mensa-id=\"" . htmlspecialchars($mensaId) . "\"" . $displayStyle . ">";

            if (empty($piatti)) {
                $piattiContent .= "<li class=\"no-dishes-message\">Oggi non ci sono piatti disponibili per questa mensa</li>";
            } else {
                foreach ($piatti as $piatto) {
                    $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];
                    $hasAllergens = $piatto->containsAllergens($userAllergeni);

                    $piattiContent .= "<li class=\"dish-card\">";
                    $piattiContent .= "<article class=\"card\">";

                    if ($piatto->getImage()) {
                        $piattiContent .= "<img src=\"" . htmlspecialchars($piatto->getImage()) . "\" alt=\"Piatto " . htmlspecialchars($piatto->getNome()) . " della mensa ESU\" class=\"piatto-img\">";
                    } else {
                        $piattiContent .= "<div class=\"no-image-placeholder\">Immagine non disponibile</div>";
                    }

                    $piattiContent .= "<div class=\"card-content\">";

                    if ($hasAllergens) {
                        $piattiContent .= "<div class=\"allergen-warning\" role=\"alert\">
                            <strong>Attenzione:</strong> Contiene allergeni da te segnalati.
                        </div>";
                    }

                    $piattiContent .= "<h4 class=\"card-title\">" . htmlspecialchars($piatto->getNome()) . "</h4>";
                    $piattiContent .= "<p class=\"card-description\">" . htmlspecialchars($piatto->getDescrizione()) . "</p>";

                    $avgVote = $piatto->getAvgVote() ?: 0;
                    $piattiContent .= "<div class=\"rating-container\">";
                    $piattiContent .= "<span class=\"rating-label\">Valutazione media:</span>";
                    $piattiContent .= "<div class=\"ratings\" aria-label=\"Valutazione: $avgVote su 5 stelle\">";
                    for ($i = 0; $i < $avgVote; $i++) {
                        $piattiContent .= $starFilledSVG;
                    }
                    for ($i = 0; $i < 5 - $avgVote; $i++) {
                        $piattiContent .= $starSVG;
                    }
                    $piattiContent .= "</div>";
                    $piattiContent .= "</div>";

                    $piattiContent .= "<div class=\"card-actions\">";
                    $piattoEncoded = urlencode($piatto->getNome());
                    $piattiContent .= "<a href=\"piatto.php?piatto=" . $piattoEncoded . "\" class=\"btn btn-primary\">Vedi le recensioni</a>";
                    $piattiContent .= "</div>";

                    $piattiContent .= "</div>";
                    $piattiContent .= "</article>";
                    $piattiContent .= "</li>";
                }
            }

            $piattiContent .= "</ul>";

            // === PIATTO DEL GIORNO ===
            $piattoDelGiorno = $datiMensa["piatto_del_giorno"];
            $dishOfTheDayContent .= "<section class=\"dish-of-the-day\" data-mensa-id=\"" . htmlspecialchars($mensaId) . "\"" . $displayStyle . ">";

            if ($piattoDelGiorno) {
                $dishOfTheDayContent .= "<h3>Piatto del giorno</h3>";
                $dishOfTheDayContent .= "<article class=\"featured-dish\">";

                if ($piattoDelGiorno->getImage()) {
                    $dishOfTheDayContent .= "<img src=\"" . htmlspecialchars($piattoDelGiorno->getImage()) . "\" alt=\"Piatto del giorno: " . htmlspecialchars($piattoDelGiorno->getNome()) . " della mensa ESU\" class=\"featured-dish-img\">";
                } else {
                    $dishOfTheDayContent .= "<div class=\"no-image-placeholder\">Immagine non disponibile</div>";
                }

                $dishOfTheDayContent .= "<div class=\"featured-dish-content\">";
                $dishOfTheDayContent .= "<h4>" . htmlspecialchars($piattoDelGiorno->getNome()) . "</h4>";
                $dishOfTheDayContent .= "<p>" . htmlspecialchars($piattoDelGiorno->getDescrizione()) . "</p>";

                $avgVote = $piattoDelGiorno->getAvgVote() ?: 0;
                $dishOfTheDayContent .= "<div class=\"rating-container\">";
                $dishOfTheDayContent .= "<span class=\"rating-label\">Valutazione:</span>";
                $dishOfTheDayContent .= "<div class=\"ratings\" aria-label=\"Valutazione: $avgVote su 5 stelle\">";
                for ($i = 0; $i < $avgVote; $i++) {
                    $dishOfTheDayContent .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $avgVote; $i++) {
                    $dishOfTheDayContent .= $starSVG;
                }
                $dishOfTheDayContent .= "</div>";
                $dishOfTheDayContent .= "</div>";

                $piattoEncoded = urlencode($piattoDelGiorno->getNome());
                $dishOfTheDayContent .= "<a href=\"piatto.php?piatto=" . $piattoEncoded . "\" class=\"btn btn-primary\">Vedi le recensioni</a>";
                $dishOfTheDayContent .= "</div>";
                $dishOfTheDayContent .= "</article>";
            } else {
                $dishOfTheDayContent .= "<p class=\"no-dish-message\">Oggi non c'è un piatto del giorno per questa mensa</p>";
            }

            $dishOfTheDayContent .= "</section>";
        }

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
            "piatti-template",
            $piattiContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "dish-of-the-day-template",
            $dishOfTheDayContent
        );

        echo $this->dom->saveHTML();
    }
}