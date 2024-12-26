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

        $count = 0;
        $mense = MenseModel::findAll();
        $tot = count($mense);
        foreach ($mense as $mensa) {

            $menseContent .=
                "<option value=\"" . $mensa->getNome() . "\">" . $mensa->getNome() . "</option>";

            $piattiContent .= "<datalist class=\"dynamic-datalist\" data-mensa-name=\"" . $mensa->getNome() . "\">";
            $currentMenu = $mensa->getCurrentMenu();
            $piatti = $currentMenu->getPiatti();
            foreach ($piatti as $piatto) {
                $piattiContent .=
                    "<option value=\"" .
                    $piatto->getNome() .
                    "\"></option>";
            }
            $piattiContent .= "</datalist>";
            // if ($count + 1 < $tot) {
            //     //    $piattiContent .= "<template id=\"suggerimenti-piatti-template\"></template>";
            //     Utils::replaceTemplateContent(
            //         $this->dom,
            //         "suggerimenti-piatti-template",
            //         $piattiContent
            //     );
            // }
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "mense-select-template",
            $menseContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-select-2-template",
            $menseContent
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "suggerimenti-piatti-template",
            $piattiContent
        );

        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );
        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );
        $starRatingSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_form.svg"
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "star-template-5",
            $starRatingSVG
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "star-template-4",
            $starRatingSVG
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "star-template-3",
            $starRatingSVG
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "star-template-2",
            $starRatingSVG
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "star-template-1",
            $starRatingSVG
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
