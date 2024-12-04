<?php
namespace Views;

use Models\PiattoModel;
use Models\RecensioneModel;
use Views\Utils;

class PiattoView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/piatto.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        $piattoTitle = "<h1 class=\"card-title\">" . $data["nome"] . "</h1>";
        $piattoDescription =
            "<div class=\"card-description\"><p>" .
            $data["descrizione"] .
            "</p></div>";

        $piatto = PiattoModel::findByName($data["nome"]);

        $piattoReview = "";

        foreach ($piatto->getRecensioni() as $recensione) {
            // $piattoReview = $recensione->getRecensione();
            $piattoReview .= "<h5>" . $recensione->getUtente() . "</h5>";
            $piattoReview .= "<p>" . $recensione->getDescrizione() . "</p>";
            $piattoReview .= "<p>" . $recensione->getVoto() . "</p>";
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "piatto-title-template",
            $piattoTitle
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "piatto-description-template",
            $piattoDescription
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "review-template",
            $piattoReview
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
