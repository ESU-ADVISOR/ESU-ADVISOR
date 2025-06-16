<?php

namespace Views;

use Models\UserModel;
use Views\Utils;

class ProfileView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/profile.html");
    }

    public function render(array $data = []): void
    {
        parent::render();
        if (empty($_SESSION["username"])) {
            self::renderError("Devi effettuare il login per accedere", 403);
            return;
        }

        $user = UserModel::findByUsername($_SESSION["username"]);
        if ($user === null) {
            self::renderError("Utente non trovato", "L'utente corrente non esiste, esci ed esegui nuovamente il login", 404);
            return;
        }

        $username = $user->getUsername();

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-username-template",
            $username
        );

        $dataNascita = $user->getDataNascita();
        $dateOfBirth = "";
        if ($dataNascita) {
            $dateOfBirth = "Nato nel <time>" . $dataNascita->format('Y') . "</time>";
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-date-of-birth-template",
            $dateOfBirth
        );

        // Handle success/error messages
        if (isset($data['success'])) {
            $successHtml = "<div class='success'>{$data['success']}</div>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        } else if (isset($data['errors'])) {
            $errorHtml = "";
            foreach ($data['errors'] as $error) {
                $errorHtml .= "<div class='error'>$error</div>";
            }
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $errorHtml
            );
        }

        $recensioni = $user->getRecensioni();
        $recensioniCount = count($recensioni);

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-recensioni-count-template",
            $recensioniCount
        );

        $avgRating = 0;
        if ($recensioniCount > 0) {
            $totalRating = 0;
            foreach ($recensioni as $recensione) {
                $totalRating += $recensione->getVoto();
            }
            $avgRating = number_format($totalRating / $recensioniCount, 1);
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-avg-rating-template",
            $avgRating
        );

        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );

        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );

        $recensioniContent = "";

        if (empty($recensioni)) {
            $recensioniContent = "<li><p class='text-center text-secondary'>Non hai ancora scritto recensioni.</p></li>";
        } else {
            foreach ($recensioni as $recensione) {
                $recensioniContent .= "<li class='review-card mb-3'>";

                $recensioniContent .= "<div class='review-header'>";
                $recensioniContent .= "<h3>" . htmlspecialchars($recensione->getPiatto()) . "</h3>";

                $recensioniContent .= "<div class='ratings' role=\"img\" aria-label='Valutazione: " .
                    $recensione->getVoto() . " su 5'>";
                for ($i = 0; $i < $recensione->getVoto(); $i++) {
                    $recensioniContent .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $recensione->getVoto(); $i++) {
                    $recensioniContent .= $starSVG;
                }
                $recensioniContent .= "</div>";
                $recensioniContent .= "</div>";

                $recensioniContent .= "<p>" . htmlspecialchars($recensione->getDescrizione()) . "</p>";

                if ($recensione->getData()) {
                    $data = $recensione->getData()->format('d/m/Y');
                    if ($recensione->isEdited()) {
                        $recensioniContent .= "<div class='review-meta'>Recensione pubblicata il: " . $data . " (modificata)</div>";
                    } else {
                        $recensioniContent .= "<div class='review-meta'>Recensione pubblicata il: " . $data . "</div>";
                    }
                }

                $recensioniContent .= "<div class='review-actions'>";
                $recensioniContent .= "<a href='piatto.php?nome=" .
                    htmlspecialchars(urldecode(str_replace(" ", "_", strtolower($recensione->getPiatto())))) .
                    "&from=profile'>Vedi dettagli piatto</a>";
                $recensioniContent .= "<a class='review-edit-button' href='review-edit.php?piatto=" .
                    htmlspecialchars(urlencode($recensione->getPiatto())) .
                    "&mensa=" . htmlspecialchars(urlencode($recensione->getMensa())) .
                    "&from=profile'>Modifica recensione</a>";
                $recensioniContent .= "</div>";

                $recensioniContent .= "</li>";
            }
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-recensioni-template",
            $recensioniContent
        );

        echo $this->dom->saveHTML();
    }
}
