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

        $piattoTitle = "<h1 class=\"card-title\">" . htmlspecialchars($data["nome"]) . "</h1>";
        $piattoDescription =
            "<div class=\"card-description\"><p>" .
            htmlspecialchars($data["descrizione"]) .
            "</p></div>";

        $piatto = PiattoModel::findByName($data["nome"]);

        // Ottieni l'immagine del piatto
        $piattoImage = "";
        if ($piatto->getImage()) {
            $piattoImage = "<img src=\"" . $piatto->getImage() . "\" alt=\"" . htmlspecialchars($data["nome"]) . "\" class=\"piatto-img\">";
        } else {
            $piattoImage = "<div class=\"no-image\">Immagine non disponibile</div>";
        }

        // Prepara le stelle per la valutazione
        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );

        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );

        $piattoRating = "<div class=\"ratings\">";
        $avgVote = $piatto->getAvgVote() ?: 0;
        for ($i = 0; $i < $avgVote; $i++) {
            $piattoRating .= $starFilledSVG;
        }
        for ($i = 0; $i < 5 - $avgVote; $i++) {
            $piattoRating .= $starSVG;
        }
        $piattoRating .= "</div>";

        // Genera la lista di recensioni in un formato migliore
        $piattoReview = "";
        $recensioni = $piatto->getRecensioni();
        
        if (empty($recensioni)) {
            $piattoReview = "<li class=\"text-center text-secondary\">Non ci sono ancora recensioni per questo piatto</li>";
        } else {
            foreach ($recensioni as $recensione) {
                $piattoReview .= "<li class=\"review-card\">";
                $piattoReview .= "<div class=\"review-header\">";
                $piattoReview .= "<h4 class=\"review-author\">" . htmlspecialchars($recensione->getUtente()) . "</h4>";
                
                // Aggiungi stelle per la valutazione
                $piattoReview .= "<div class=\"ratings\">";
                for ($i = 0; $i < $recensione->getVoto(); $i++) {
                    $piattoReview .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $recensione->getVoto(); $i++) {
                    $piattoReview .= $starSVG;
                }
                $piattoReview .= "</div>";
                $piattoReview .= "</div>";
                
                // Aggiungi il testo della recensione
                $piattoReview .= "<p class=\"review-text\">" . htmlspecialchars($recensione->getDescrizione()) . "</p>";
                
                // Aggiungi metadati della recensione come la data
                if ($recensione->getData()) {
                    $data = $recensione->getData()->format('d/m/Y');
                    $piattoReview .= "<div class=\"review-meta\">Recensione pubblicata il: " . $data . "</div>";
                }
                
                $piattoReview .= "</li>";
            }
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
            "piatto-image-template",
            $piattoImage
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "piatto-rating-template",
            $piattoRating
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