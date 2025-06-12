<?php

namespace Views;

use Models\MenseModel;
use Models\RecensioneModel;
use Views\Utils;

class ReviewEditView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/review-edit.html");
    }

    public function render(array $data = []): void
    {
        // ✅ La modifica recensioni è accessibile SOLO da profile.php
        // Breadcrumb sempre: Profilo > Modifica Recensione
        $this->setBreadcrumb([
            'parent' => [
                'url' => 'profile.php',
                'title' => 'Profilo'
            ],
            'current' => "Modifica Recensione",
        ]);
        parent::render();

        if (empty($_SESSION["username"])) {
            self::renderError("Non hai effettuato il <span lang='en'>login</span>, impossibile modificare una recensione.", 403);
            return;
        }

        if (!isset($data['recensione']) || !$data['recensione'] instanceof RecensioneModel) {
            self::renderError("Recensione non trovata", "Nessuna recensione trovata da modificare.", 404);
            return;
        }

        $recensione = $data['recensione'];
        $menseContent = "";

        $mense = MenseModel::findAll();
        foreach ($mense as $mensa) {
            $selected = "";
            $piatti = $mensa->getPiatti();

            foreach ($piatti as $piatto) {
                if ($piatto->getNome() === $recensione->getPiatto()) {
                    $selected = " selected";
                    break;
                }
            }

            $menseContent .= "<option value=\"" . $mensa->getNome() . "\"" . $selected . ">" . $mensa->getNome() . "</option>";
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "mense-select-template",
            $menseContent
        );


        $piattoInput = $this->dom->getElementById('piatto');
        if ($piattoInput) {
            $piattoInput->setAttribute('value', $recensione->getPiatto());
        }
        Utils::replaceTemplateContent(
            $this->dom,
            "hidden-piatto-value-template",
            '<input type="hidden"  name="piatto" value="' . $recensione->getPiatto() . '" />'
        );

        $reviewTextarea = $this->dom->getElementById('review');
        if ($reviewTextarea) {
            $reviewTextarea->textContent = $recensione->getDescrizione();
        }

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

        $voto = $recensione->getVoto();
        if ($voto >= 1 && $voto <= 5) {
            $ratingInput = $this->dom->getElementById('star' . $voto);
            if ($ratingInput) {
                $ratingInput->setAttribute('checked', 'checked');
            }
        }

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
        } else if (isset($data["success"])) {
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