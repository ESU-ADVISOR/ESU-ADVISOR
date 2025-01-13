<?php

namespace Views;

use Models\MenseModel;
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
            foreach ($data["mense"] as $mensa) {
                // Build menseContent
                $menseContent .=
                    "<option value=\"" .
                    htmlspecialchars($id) .
                    "\">" .
                    htmlspecialchars($mensa["nome"]) .
                    "</option>";

                $menseInfoContent .= "<li class=\"mense-info-item\" hidden data-mensa-id=\"" .
                    htmlspecialchars($id) . "\">";

                // Heading for the mensa section
                $menseInfoContent .= "<h3>" . htmlspecialchars($mensa["nome"]) . "</h3>";

                // Contact information list
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

                // The table will be appended here later
                $menseInfoContent .= "<div class=\"schedule-container\">";
                // Fetch orari
                $orari = MenseModel::findByName(
                    $mensa["nome"]
                )->getMenseOrari();

                // Weekdays
                $giorniSettimana = [
                    "Lunedì",
                    "Martedì",
                    "Mercoledì",
                    "Giovedì",
                    "Venerdì",
                    "Sabato",
                    "Domenica",
                ];

                // Initialize table
                $menseInfoContent .= "<table> <caption>Orari Mensa</caption><thead>
                    <tr>
                        <th>Giorno</th>
                        <th>Inizio</th>
                        <th>Fine</th>
                    </tr>
                </thead><tbody>";

                foreach ($orari as $orario) {
                    $giornoSettimanaIndex =
                        intval($orario["giornoSettimana"]) - 1;
                    if (
                        $giornoSettimanaIndex >= 0 &&
                        $giornoSettimanaIndex < count($giorniSettimana)
                    ) {
                        $giorno = htmlspecialchars(
                            $giorniSettimana[$giornoSettimanaIndex]
                        );
                    } else {
                        $giorno = "N/A";
                    }

                    $orainizio = htmlspecialchars($orario["orainizio"]);
                    $orafine = htmlspecialchars($orario["orafine"]);

                    $menseInfoContent .= "<tr>
                        <th>{$giorno}</th>
                        <td>{$orainizio}</td>
                        <td>{$orafine}</td>
                    </tr>";
                }
                $menseInfoContent .= "</tbody></table></div>";

                // Add maps link
                $menseInfoContent .=
                    "<a href=\"" .
                    htmlspecialchars($mensa["maps_link"]) .
                    "\">
                    <button class=\"nav-button secondary\">Direzioni</button>
                </a></li>";

                // Handle piatti
                if (isset($mensa["piatti"]) && is_array($mensa["piatti"])) {
                    foreach ($mensa["piatti"] as $piatto) {
                        $piattiContent .=
                            "<li><article class=\"menu-item\" hidden data-mensa-id=\"" .
                            htmlspecialchars($id) .
                            "\">";
                        if ($piatto->getImage()) {
                            $piattiContent .=
                                "<figure><img src=\"" . $piatto->getImage() . "\" alt=\"" .
                                htmlspecialchars($piatto->getNome()) .
                                "\" width=\"150\" height=\"80\"></figure>";
                        } else {
                            $piattiContent .=
                                "<figure><img src=\"\" alt=\"" .
                                htmlspecialchars($piatto->getNome()) .
                                "\" width=\"150\" height=\"80\"></figure>";
                        }
                        $piattiContent .=
                            "<div class=\"menu-item-content\">" .
                            "<h3>" .
                            htmlspecialchars($piatto->getNome()) .
                            "</h3>";
                        $piattiContent .=
                            "<p>" .
                            htmlspecialchars($piatto->getDescrizione()) .
                            "</p>";
                        $piattiContent .= "<div class=\"ratings\">";
                        for ($i = 0; $i < $piatto->getAvgVote(); $i++) {
                            $piattiContent .= $starFilledSVG;
                        }
                        for ($i = 0; $i < 5 - $piatto->getAvgVote(); $i++) {
                            $piattiContent .= $starSVG;
                        }
                        $piattiContent .= "</div>";
                        $piattiContent .=
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
                            "</article></li>";
                    }
                }

                if (
                    isset($mensa["piatto_del_giorno"]) &&
                    $mensa["piatto_del_giorno"]
                ) {
                    $dishOfTheDayContent .=
                        "<article class=\"menu-item\" hidden data-mensa-id=\"" .
                        htmlspecialchars($id) .
                        "\">";
                    $dishOfTheDayContent .=
                        "<figure><img src=\"\" alt=\"" .
                        htmlspecialchars(
                            $mensa["piatto_del_giorno"]->getNome()
                        ) .
                        "\" width=\"150\" height=\"80\"></figure>";
                    $dishOfTheDayContent .= "<div class=\"menu-item-content\">";
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
                    $dishOfTheDayContent .= "<div class=\"ratings\">";
                    for (
                        $i = 0;
                        $i < $mensa["piatto_del_giorno"]->getAvgVote();
                        $i++
                    ) {
                        $dishOfTheDayContent .= $starFilledSVG;
                    }
                    for (
                        $i = 0;
                        $i < 5 - $mensa["piatto_del_giorno"]->getAvgVote();
                        $i++
                    ) {
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
                        "\">Vedi recensioni</a>" .
                        "</div>" .
                        "</article>";
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

        $html = mb_convert_encoding($html, "UTF-8", "HTML-ENTITIES");

        // Output the HTML
        echo $html;
    }
}
