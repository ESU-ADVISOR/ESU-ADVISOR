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
        if (empty($_SESSION["email"])) {
            self::renderError("You're not logged in");
            return;
        }
        $user = UserModel::findByEmail($_SESSION["email"]);

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

        $recensioniContent = "";

        $recensioni = $user->getRecensioni();
        foreach ($recensioni as $recensione) {
            $recensioniContent .=
                "<li><h4>" . $recensione->getPiatto() . "<h4/>";
            $recensioniContent .=
                "<p>" . $recensione->getDescrizione() . "</p>";
            $recensioniContent .= $recensione->getVoto() . "</li>";
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "profile-recensioni-template",
            $recensioniContent
        );

        echo $this->dom->saveHTML();
    }
}
