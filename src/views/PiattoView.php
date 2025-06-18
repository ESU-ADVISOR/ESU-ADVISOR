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

        $mensa = $data["mensa"] ?? null;

        $piattoTitle = "<h2 class=\"card-title\">" . htmlspecialchars($data["nome"]) . "</h2>";

        $piatto = PiattoModel::findByName($data["nome"]);

        $userAllergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];

        $piattoDescription = "<div class=\"card-description\">";
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

        $avgVote = $piatto->getAvgVote() ?: 0;
        $piattoRating = "<div class=\"ratings\" role=\"img\" aria-label=\"" . $avgVote . " su 5\">";
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
                $allergeniFormatted = [];
                foreach ($allergeni as $allergene) {
                    if (in_array($allergene, $userAllergeni)) {
                        $allergeniFormatted[] = "<span class=\"allergen-highlighted\">" . htmlspecialchars($allergene) . "</span>";
                    } else {
                        $allergeniFormatted[] = htmlspecialchars($allergene);
                    }
                }
                $allergeniContent = "<p>" . implode(", ", $allergeniFormatted) . "</p>";
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
            foreach ($mense as $mensaNome) {
                $menseContent .= "<li>" . htmlspecialchars($mensaNome) . "</li>";
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
                $piattoReview .= "<h4 class=\"review-author\">" . htmlspecialchars($recensione->getUsername()) . "</h4>";

                $piattoReview .= "<div class=\"ratings\" role=\"img\" aria-label=\"Valutazione: " . $recensione->getVoto() . " su 5\">";
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
                    $date = $recensione->getData()->format('d/m/Y');
                    $piattoReview .= "<div class=\"review-meta\">Recensione pubblicata il: " . $date . "</div>";
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

        // Generate review button with mensa and piatto parameters
        $reviewButtonQuery = "?piatto=" . urlencode($data["nome"]);
        if ($mensa) {
            $reviewButtonQuery .= "&mensa=" . urlencode($mensa);
        }

        $reviewButtonHtml = "<a href=\"review.php" . $reviewButtonQuery . "\" class=\"nav-button primary\">
                                <svg
                                    xmlns=\"http://www.w3.org/2000/svg\"
                                    width=\"20\"
                                    height=\"20\"
                                    viewBox=\"0 0 24 24\"
                                    fill=\"none\"
                                    stroke=\"currentColor\"
                                    stroke-width=\"2\"
                                    stroke-linecap=\"round\"
                                    stroke-linejoin=\"round\"
                                >
                                    <path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path>
                                    <path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path>
                                </svg>
                                Scrivi una recensione
                            </a>";

        Utils::replaceTemplateContent(
            $this->dom,
            "review-button-template",
            $reviewButtonHtml
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

        // Add script to store mensa in sessionStorage for back navigation
        if (!empty($mensa)) {
            $script = $this->dom->createElement('script');
            $scriptContent = "sessionStorage.setItem('currentMensa', " . json_encode($mensa) . ");";
            $script->appendChild($this->dom->createTextNode($scriptContent));
            $this->dom->getElementsByTagName('head')->item(0)->appendChild($script);
        }

        echo $this->dom->saveHTML();
    }
}
