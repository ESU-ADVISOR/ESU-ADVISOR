<?php

namespace Views;

use Models\MenseModel;
use Views\Utils;

class ReviewView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/review.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (empty($_SESSION["username"])) {
            self::renderError("Non hai effettuato il <span lang='en'>login</span>, impossibile inviare una recensione.", 403);
            return;
        }

        $prefilledMensa = $data["mensa"] ?? "";
        $prefilledPiatto = $data["piatto"] ?? "";

        $menseContent = "";
        $piattiContent = "";

        $mense = MenseModel::findAll();
        foreach ($mense as $mensa) {
            $selected = ($prefilledMensa === $mensa->getNome()) ? " selected" : "";
            $menseContent .=
                "<option class=\"mensa-option\" value=\"" . $mensa->getNome() . "\"" . $selected . ">" . $mensa->getNome() . "</option>";

            $piattiContent .= "<datalist class=\"dynamic-datalist\" data-mensa-name=\"" . $mensa->getNome() . "\">";
            $piatti = $mensa->getPiatti();
            foreach ($piatti as $piatto) {
                $piattiContent .=
                    "<option value=\"" .
                    $piatto->getNome() .
                    "\"></option>";
            }
            $piattiContent .= "</datalist>";
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
                $errorHtml .= "<div class='error' role='alert' aria-live='assertive'>$error</div>";
            }
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $errorHtml
            );
        }

        if (isset($data["success"])) {
            $successHtml = "<div class='success' role='polite' aria-live='region'>{$data["success"]}</div>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        }

        if (!empty($prefilledPiatto)) {
            $piattoInput = $this->dom->getElementById('piatto');
            if ($piattoInput) {
                $piattoInput->setAttribute('value', $prefilledPiatto);
            }
        }

        if (!empty($prefilledMensa) || !empty($prefilledPiatto)) {
            $body = $this->dom->getElementsByTagName('body')->item(0);
            if (!empty($prefilledMensa)) {
                $body->setAttribute('data-prefilled-mensa', $prefilledMensa);
            }
            if (!empty($prefilledPiatto)) {
                $body->setAttribute('data-prefilled-piatto', $prefilledPiatto);
            }
        }

        echo $this->dom->saveHTML();
    }
}
