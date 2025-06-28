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
        if (isset($data["mensa_iniziale"]) && !empty($data["mensa_iniziale"])) {
            $mensaIniziale = $data["mensa_iniziale"];

            $this->setTitle("Menu $mensaIniziale | ESU Advisor");

            $this->setDescription("Consulta il menu di oggi di $mensaIniziale a Padova. Scopri piatti, orari e recensioni delle mense universitarie ESU.");

            $this->setKeywords("$mensaIniziale,menu,mensa,orari,ESU,Padova,università,piatti,recensioni,allergeni");
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

        foreach (MenseModel::findAll() as $datiMensa) {
            $selected = ($datiMensa->getNome() === $mensaIniziale) ? 'selected' : '';
            $menseContent .= "<option value=\"" . htmlspecialchars($datiMensa->getNome()) . "\" " . $selected . ">" . htmlspecialchars($datiMensa->getNome()) . "</option>";
        }

        $datiMensa = $menseComplete[0]; 
        $mensaId = $datiMensa["nome"];

        // === MENSE INFO ===
        $menseInfoContent .= "<a href=\"./mensa.php?mensa=" . urlencode($mensaId) . "\" class=\"nav-button primary\" id=\"mensa-info-button\">Visualizza Informazioni</a>";
        // === PIATTI DEL MENU ==
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
            "mense-info-template-link",
            $menseInfoContent
        );

        echo $this->dom->saveHTML();
    }
}
