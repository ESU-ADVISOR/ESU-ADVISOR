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
        $memberSince = "";
        if ($dataNascita) {
            $memberSince = "Membro dal <time>" . $dataNascita->format('Y') . "</time>";
        } else {
            $memberSince = "Membro";
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-member-since-template",
            $memberSince
        );

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
                $recensioniContent .= "<h4>" . htmlspecialchars($recensione->getPiatto()) . "</h4>";

                $recensioniContent .= "<div class='ratings'>";
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
                    urldecode(str_replace(" ", "_", strtolower($recensione->getPiatto()))) .
                    "'>Vedi dettagli piatto</a>";
                $recensioniContent .= "<a href='review-edit.php?piatto=" .
                    htmlspecialchars(urlencode($recensione->getPiatto())) .
                    "'>Modifica recensione</a>";
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
