<?php
namespace Views;

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

        $menseContent = "";
        if (isset($data["mense"]) && is_array($data["mense"])) {
            foreach ($data["mense"] as $mensa) {
                //print_r($mensa);
                //$menseContent .= "<li>" . htmlspecialchars($mensa) . "</li>";
                $menseContent .= "<option value=\"" . htmlspecialchars($mensa["id"]) . "\">" . htmlspecialchars($mensa["nome"]) . "</option>";
            }
        }

        $menuContent = "";
        if (isset($data["menu"]) && is_array($data["menu"])) {
            foreach ($data["menu"] as $menuItem) {
                $menuContent .= "<li>" . htmlspecialchars($menuItem) . "</li>";
            }
        }

        $piattiContent = "";
        if (isset($data["piatti"]) && is_array($data["piatti"])) {
            foreach ($data["piatti"] as $piatto) {
                $piattiContent .= "<dt>" . htmlspecialchars($piatto["nome"]) . "</dt>";
                $piattiContent .= "<dd>" . htmlspecialchars($piatto["descrizione"]) . "</dd>";
                $piattiContent .= "<dd><img src=\"images/logo.png\" alt=\"" . htmlspecialchars($piatto["nome"]) . "\" width=\"auto\" height=\"50\"></dd>";
                $piattiContent .= "<svg
                                        class=\"star filled\"
                                        xmlns=\"http://www.w3.org/2000/svg\"
                                        viewBox=\"0 0 24 24\"
                                        width=\"24\"
                                        height=\"24\"
                                        fill=\"currentColor\"
                                    >
                                        <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2z\"/>
                                    </svg>";
                $piattiContent .= "<svg
                                        class=\"star\"
                                        xmlns=\"http://www.w3.org/2000/svg\"
                                        viewBox=\"0 0 24 24\"
                                        width=\"24\"
                                        height=\"24\"
                                        fill=\"currentColor\" 
                                    >
                                        <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2z\"/>
                                    </svg>";
                $piattiContent .= "<a href=\"./piatto.html\">Vedi recensioni</a>";
            }
        }

        $dishOfTheDayContent = "";
        $dishOfTheDayContent .= "<dt>Nome piatto</dt>";
        $dishOfTheDayContent .= "<dd>Descrizione piatto</dd>";
        $dishOfTheDayContent .= "<dd><img src=\"images/logo.png\" alt=\"Foto piatto del giorno\" width=\"auto\" height=\"50\"></dd>";
        $dishOfTheDayContent .= "<svg
                                class=\"star filled\"
                                xmlns=\"http://www.w3.org/2000/svg\"
                                viewBox=\"0 0 24 24\"
                                width=\"24\"
                                height=\"24\"
                                fill=\"currentColor\"
                            >
                                <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2z\"/>
                            </svg>";
        $dishOfTheDayContent .= "<svg
                                class=\"star\"
                                xmlns=\"http://www.w3.org/2000/svg\"
                                viewBox=\"0 0 24 24\"
                                width=\"24\"
                                height=\"24\"
                                fill=\"currentColor\" 
                            >
                                <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2z\"/>
                            </svg>";

        $menseInfoContent = "";
        $menseInfoContent .= "<dt>Nome mensa</dt>";
        $menseInfoContent .= "<dd>Indirizzo: via roma</dd>";
        $menseInfoContent .= "<dd>Telefono mensa: 1234567890</dd>";
        $menseInfoContent .= "<dd>Orari mensa: 00.00 - 23.59</dd>";
        $menseInfoContent .= "<button>Direzioni</button>";


        Utils::replaceTemplateContent(
            $this->dom,
            "mense-template",
            $menseContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "menu-template",
            $menuContent
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
?>
