<?php

namespace Views;

use Models\DimensioneIcone;
use Models\DimensioneTesto;
use Models\FiltroDaltonici;
use Models\RecensioneModel;
use Models\UserModel;
use Models\MenseModel;
use Models\ModificaFont;
use Models\PreferenzeUtenteModel;
use Views\Utils;

class SettingsView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/settings.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (empty($_SESSION["username"])) {
            self::renderError("You're not logged in");
            return;
        }

        $menseContent = "";
        $mense = MenseModel::findAll();
        $user = UserModel::findByUsername($_SESSION["username"]);
        $userPreferences = PreferenzeUtenteModel::findByEmail($user->getEmail());

        if ($user === null) {
            self::renderError("User not found");
            return;
        }

        // ======== Dark Mode =========

        $darkModeContent = "";

        if ($userPreferences != null && $userPreferences->isDarkMode()) {
            $darkModeContent .= '<input type="checkbox" id="theme-toggle" name="dark_mode" class="visually-hidden" checked/>';
        } else {
            $darkModeContent .= '<input type="checkbox" id="theme-toggle" name="dark_mode" class="visually-hidden"/>';
        }
        Utils::replaceTemplateContent(
            $this->dom,
            "dark-mode-option-template",
            $darkModeContent
        );

        // ======== Dimensione Testo =========

        $dimensioneTestoContent = "";

        $opzioniDimensioneTesto = DimensioneTesto::cases();
        $hasDimensioneTesto = false;

        foreach ($opzioniDimensioneTesto as $opzione) {
            if ($userPreferences != null && $userPreferences->getDimensioneTesto()->value == $opzione->value) {
                $hasDimensioneTesto = true;
                $dimensioneTestoContent .= '<option value="' . $opzione->value . '" selected>' . $opzione->value . '</option>';
            } else {
                $dimensioneTestoContent .= '<option value="' . $opzione->value . '">' . $opzione->value . '</option>';
            }
        }

        if (!$hasDimensioneTesto) {
            $dimensioneTestoContent = '<option value="none" selected>None</option>' . $dimensioneTestoContent;
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-testo-options-template",
            $dimensioneTestoContent
        );

        // ======== Dimensione Icone =========

        $dimensioneIconeContent = "";

        $opzioniDimensioneIcone = DimensioneIcone::cases();
        $hasDimensioneIcone = false;

        foreach ($opzioniDimensioneIcone as $opzione) {
            if ($userPreferences != null && $userPreferences->getDimensioneIcone()->value == $opzione->value) {
                $hasDimensioneIcone = true;
                $dimensioneIconeContent .= '<option value="' . $opzione->value . '" selected>' . $opzione->value . '</option>';
            } else {
                $dimensioneIconeContent .= '<option value="' . $opzione->value . '">' . $opzione->value . '</option>';
            }
        }

        if (!$hasDimensioneIcone) {
            $dimensioneIconeContent = '<option value="none" selected>None</option>' . $dimensioneIconeContent;
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-icone-options-template",
            $dimensioneIconeContent
        );

        // ======== Modifica font =========

        $fontContent = "";

        $opzioniFont = ModificaFont::cases();
        $hasFont = false;

        foreach ($opzioniFont as $opzione) {
            if ($userPreferences != null && ($userPreferences->getModificaFont()->value == $opzione->value) || ($opzione->value == "none" && $userPreferences == null)) {
                $hasFont = true;
                $fontContent .= '<option value="' . $opzione->value . '" selected>' . $opzione->value . '</option>';
            } else {
                $fontContent .= '<option value="' . $opzione->value . '">' . $opzione->value . '</option>';
            }
        }

        if (!$hasFont) {
            $fontContent = '<option value="none" selected>None</option>' . $fontContent;
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "font-options-template",
            $fontContent
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
