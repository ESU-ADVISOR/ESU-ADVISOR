<?php
namespace Views;

use Models\MenseModel;
use Models\PiattoModel;
use Models\RecensioneModel;
use Models\UserModel;
use Views\Utils;

class ForYouPageView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/fyp.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (empty($_SESSION["email"])) {
            self::renderError("You're not logged in");
            return;
        }

        $menseContent = "";
        $piattiContent = "";

        foreach (MenseModel::findAll() as $mensa) {
            $menseContent .=
                "<option value=\"" . $mensa->getNome() . "\"></option>";
            $currentMenu = $mensa->getCurrentMenu();
            $piatti = $currentMenu->getPiatti();
            foreach ($piatti as $piatto) {
                $piattiContent .=
                    "<option value=\"" .
                    $piatto->getNome() .
                    "\" data-mensa-name=\"" .
                    $mensa->getNome() .
                    "\"></option>";
            }
        }

        foreach ($piatti as $piatto) {
        }
        Utils::replaceTemplateContent(
            $this->dom,
            "suggerimenti-mense-template",
            $menseContent
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "suggerimenti-piatti-template",
            $piattiContent
        );

        if (isset($data["errors"])) {
            $errorHtml = "";
            foreach ($data["errors"] as $error) {
                $errorHtml .= "<div class='error'>$error</div>";
            }
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $errorHtml
            );
        }

        if (isset($data["success"])) {
            $successHtml = "<div class='success'>{$data["success"]}</div>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        }
        echo $this->dom->saveHTML();
    }
}
?>
