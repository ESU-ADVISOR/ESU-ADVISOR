<?php

namespace Views;

use Models\PiattoModel;
use Views\Utils;
use Views\BaseView;

class PiattoView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/piatto.html");
    }

    public function render(array $data = []): void
    {
        // Personalizza SEO per questo specifico piatto
        if (isset($data["nome"])) {
            $nomePiatto = $data["nome"];
            
            // Title dinamico con nome del piatto
            $this->setTitle("$nomePiatto - Recensioni e Dettagli | ESU Advisor");
            
            // Description con ingredienti del piatto
            $descrizione = $data["descrizione"] ?? "";
            $this->setDescription("Scopri tutto su $nomePiatto delle mense ESU di Padova: $descrizione Leggi recensioni degli studenti, ingredienti e allergeni.");
            
            // Keywords specifiche per il piatto
            $this->setKeywords("$nomePiatto, recensioni $nomePiatto, piatto mensa padova, valutazioni studenti, ingredienti $nomePiatto, allergeni");
            
            // Gestisci breadcrumb in base alla provenienza
            $fromProfile = isset($_GET['from']) && $_GET['from'] === 'profile';
            
            if ($fromProfile) {
                // Breadcrumb: Profilo > [Nome Piatto]
                $this->setBreadcrumb([
                    'parent' => [
                        'url' => 'profile.php',
                        'title' => 'Profilo'
                    ],
                    'current' => $nomePiatto
                ]);
            } else {
                // Breadcrumb: Home > [Nome Piatto] (comportamento standard)
                $this->setBreadcrumb([
                    'parent' => [
                        'url' => 'index.php',
                        'title' => 'Home'
                    ],
                    'current' => $nomePiatto
                ]);
            }
        }

        parent::render();

        $piattoTitle = "<h2 class=\"card-title\" id=\"primoContenuto\">" . htmlspecialchars($data["nome"]) . "</h2>";

        $piatto = PiattoModel::findByName($data["nome"]);

        $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];
        $hasAllergens = $piatto->containsAllergens($userAllergeni);

        $piattoDescription = "<div class=\"card-description\">";

        if ($hasAllergens) {
            $piattoDescription .= "<div class=\"allergen-warning\" role=\"alert\">
                <strong>Attenzione:</strong> Questo piatto contiene allergeni da te segnalati.
            </div>";
        }

        $piattoDescription .= "<p>" . htmlspecialchars($data["descrizione"]) . "</p>";
        $piattoDescription .= "</div>";

        $piattoImage = "";
        if ($piatto->getImage()) {
            $piattoImage = "<img src=\"" . $piatto->getImage() . "\" alt=\"Piatto " . htmlspecialchars($data["nome"]) . " della mensa ESU\" class=\"piatto-img\">";
        } else {
            $piattoImage = "<div class=\"no-image\">Immagine non disponibile</div>";
        }

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

        // Gestione allergeni
        $allergeniContent = "";
        $allergeni = $piatto->getAllergeni();
        if (!empty($allergeni)) {
            // Rimuovi "Nessuno" dalla lista se presente insieme ad altri allergeni
            $allergeni = array_filter($allergeni, function ($allergene) {
                return $allergene !== "Nessuno";
            });

            if (!empty($allergeni)) {
                $allergeniContent = "<p>" . htmlspecialchars(implode(", ", $allergeni)) . "</p>";
            } else {
                $allergeniContent = "<p>Nessuno</p>";
            }
        } else {
            $allergeniContent = "<p>Nessuno</p>";
        }

        // Gestione mense
        $menseContent = "";
        $mense = $piatto->getMense();
        if (!empty($mense)) {
            $menseContent = "<ul>";
            foreach ($mense as $mensa) {
                $menseContent .= "<li>" . htmlspecialchars($mensa) . "</li>";
            }
            $menseContent .= "</ul>";
        } else {
            $menseContent = "<p class=\"text-secondary\">Questo piatto non è attualmente disponibile in nessuna mensa</p>";
        }

        $piattoReview = "";
        $recensioni = $piatto->getRecensioni();

        if (empty($recensioni)) {
            $piattoReview = "<li class=\"text-center text-secondary\">Non ci sono ancora recensioni per questo piatto</li>";
        } else {
            foreach ($recensioni as $recensione) {
                $piattoReview .= "<li class=\"review-card\">";
                $piattoReview .= "<div class=\"review-header\">";
                $piattoReview .= "<h4 class=\"review-author\">" . htmlspecialchars($recensione->getUtente()) . "</h4>";

                $piattoReview .= "<div class=\"ratings\">";
                for ($i = 0; $i < $recensione->getVoto(); $i++) {
                    $piattoReview .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $recensione->getVoto(); $i++) {
                    $piattoReview .= $starSVG;
                }
                $piattoReview .= "</div>";
                $piattoReview .= "</div>";

                $piattoReview .= "<p class=\"review-text\">" . htmlspecialchars($recensione->getDescrizione()) . "</p>";

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
            "piatto-allergeni-template",
            $allergeniContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "piatto-mense-template",
            $menseContent
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