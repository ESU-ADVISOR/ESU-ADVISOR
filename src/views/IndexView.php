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
                $piattiContent .= "<li>" . htmlspecialchars($piatto["nome"]) . "</li>";
            }
        }

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

        echo $this->dom->saveHTML();
    }
}
?>
