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
            self::renderError("You're not logged in");
            return;
        }
        $user = UserModel::findByUsername($_SESSION["username"]);
        if ($user === null) {
            self::renderError("User not found");
            return;
        }

        $username = $user->getUsername();

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-username-template",
            $username
        );

        // Miglioramento della visualizzazione delle recensioni con stelle e card
        $starSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star.svg"
        );

        $starFilledSVG = file_get_contents(
            __DIR__ . "/../../public_html/images/star_filled.svg"
        );

        $recensioniContent = "";
        $recensioni = $user->getRecensioni();
        
        if (empty($recensioni)) {
            $recensioniContent = "<p class='text-center text-secondary'>Non hai ancora scritto recensioni.</p>";
        } else {
            foreach ($recensioni as $recensione) {
                $recensioniContent .= "<li class='review-card mb-3'>";
                $recensioniContent .= "<h4>" . htmlspecialchars($recensione->getPiatto()) . "</h4>";
                $recensioniContent .= "<p>" . htmlspecialchars($recensione->getDescrizione()) . "</p>";
                
                // Aggiungi stelle anziché numeri
                $recensioniContent .= "<div class='ratings'>";
                for ($i = 0; $i < $recensione->getVoto(); $i++) {
                    $recensioniContent .= $starFilledSVG;
                }
                for ($i = 0; $i < 5 - $recensione->getVoto(); $i++) {
                    $recensioniContent .= $starSVG;
                }
                $recensioniContent .= "</div>";
                
                // Aggiungi metadati della recensione
                if ($recensione->getData()) {
                    $data = $recensione->getData()->format('d/m/Y');
                    $recensioniContent .= "<div class='review-meta'>Recensione pubblicata il: " . $data . "</div>";
                }
                
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