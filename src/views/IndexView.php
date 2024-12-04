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

        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            $welcomeMessage =
                "Welcome, " . htmlspecialchars($_SESSION["username"]);
            Utils::replaceTemplateContent(
                $this->dom,
                "welcome-template",
                $welcomeMessage
            );
        }

        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );

        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );

        $menseContent = "";
        $menseInfoContent = "";
        $piattiContent = "";

        if (isset($data["mense"]) && is_array($data["mense"])) {
            $id = 0;
            foreach ($data["mense"] as $mensa) {
                $menseContent .=
                    "<option value=\"" .
                    $id .
                    "\">" .
                    htmlspecialchars($mensa["nome"]) .
                    "</option>";

                $menseInfoContent .=
                    "<div class=\"mense-info-item\" hidden data-mensa-id=\"" .
                    $id .
                    "\">";
                $menseInfoContent .= "<dt>" . $mensa["nome"] . "</dt>";
                $menseInfoContent .=
                    "<dd>Indirizzo: " . $mensa["indirizzo"] . "</dd>";
                $menseInfoContent .= "<dd>Telefono mensa: 1234567890</dd>";

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
                $menseInfoContent .=
                    "<table> <caption>Orari mensa</caption><tbody>";

                foreach ($orari as $orario) {
                    $menseInfoContent .=
                        "<tr><th>" .
                        $giorniSettimana[
                            intval($orario["giornoSettimana"]) - 1
                        ] .
                        "</th><td>" .
                        $orario["orainizio"] .
                        "</td><td>" .
                        $orario["orafine"] .
                        "</td></tr>";
                }
                $menseInfoContent .= "</tbody></table>";

                $menseInfoContent .=
                    "<button class=\"nav-button secondary\">Direzioni</button></div>";

                if (isset($mensa["piatti"]) && is_array($mensa["piatti"])) {
                    /**
                     * @var PiattoModel $piatto
                     */
                    foreach ($mensa["piatti"] as $piatto) {
                        $piattiContent .=
                            "<article class=\"menu-item\" hidden data-mensa-id=\"" .
                            $id .
                            "\">";
                        $piattiContent .=
                            "<figure><img src=\"images/logo.png\" alt=\"" .
                            htmlspecialchars($piatto->getNome()) .
                            "\" width=\"auto\" height=\"50\"></figure>";
                        $piattiContent .=
                            "<div class=\"menu-item-content\">" .
                            "<h3>" .
                            htmlspecialchars($piatto->getNome()) .
                            "</h3>";
                        $piattiContent .=
                            "<p>" .
                            htmlspecialchars($piatto->getDescrizione()) .
                            "</p>";

                        $piattiContent .=
                            "<div class=\"ratings\">" . $starFilledSVG;
                        $piattiContent .= $starSVG . "</div>";
                        $piattiContent .=
                            "" .
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
                }

                $id++;
            }
        }

        $dishOfTheDayContent = "";
        $dishOfTheDayContent .= "<dt>Nome piatto</dt>";
        $dishOfTheDayContent .= "<dd>Descrizione piatto</dd>";
        $dishOfTheDayContent .=
            "<dd><img src=\"images/logo.png\" alt=\"Foto piatto del giorno\" width=\"auto\" height=\"50\"></dd>";
        $dishOfTheDayContent .= $starFilledSVG;
        $dishOfTheDayContent .= $starSVG;

        // $menseInfoContent = "";
        // $menseInfoContent .= "<dt>Nome mensa</dt>";
        // $menseInfoContent .= "<dd>Indirizzo: via roma</dd>";
        // $menseInfoContent .= "<dd>Telefono mensa: 1234567890</dd>";
        // $menseInfoContent .= "<dd>Orari mensa: 00.00 - 23.59</dd>";
        // $menseInfoContent .=
        //     "<button class=\"nav-button secondary\">Direzioni</button>";

        Utils::replaceTemplateContent(
            $this->dom,
            "mense-template",
            $menseContent
        );
        // Utils::replaceTemplateContent(
        //     $this->dom,
        //     "menu-template",
        //     $menuContent
        // );
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
?>
