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

        if (empty($_SESSION["email"])) {
            self::renderError("You're not logged in");
            return;
        }

        $menseContent = "";
        $mense = MenseModel::findAll();
        $user = UserModel::findByEmail($_SESSION["email"]);
        $userPreferences = PreferenzeUtenteModel::findByEmail($_SESSION["email"]);

        if ($user === null) {
            self::renderError("User not found");
            return;
        }

        // ======== Mensa =========

        $hasMensa = false;
        foreach ($mense as $mensa) {
            if ($userPreferences != null && $userPreferences->getMensaPreferita() == $mensa->getNome()) {
                $hasMensa = true;
                $menseContent .= "<option value=\"" . $mensa->getNome() . "\" selected>" . $mensa->getNome() . "</option>";
            } else {
                $menseContent .= "<option value=\"" . $mensa->getNome() . "\">" . $mensa->getNome() . "</option>";
            }
        }

        if (!$hasMensa) {
            $menseContent = "<option value=\"none\" selected>None</option>" . $menseContent;
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "mensa-options-template",
            $menseContent
        );


        // ======== Preferenze Menù (nothing to implement rn, so its gonna be empty) =========

        // ======== Preferenze Allergeni =========

        $allergeniContent = "";

        if ($userPreferences != null && $userPreferences->isAllergeneGlutine()) {
            $allergeniContent .= '<input type="checkbox" name="allergia_glutine" value="glutine" checked/> <label> Glutine</label><br>';
        } else {
            $allergeniContent .= '<input type="checkbox" name="allergia_glutine" value="glutine"/> <label> Glutine</label><br>';
        }

        if ($userPreferences != null && $userPreferences->isAllergeneLattosio()) {
            $allergeniContent .= '<input type="checkbox" name="allergia_latte" value="latte" checked/> <label> Latte</label><br>';
        } else {
            $allergeniContent .= '<input type="checkbox" name="allergia_latte" value="latte"/> <label> Latte</label><br>';
        }

        if ($userPreferences != null && $userPreferences->isAllergeneUova()) {
            $allergeniContent .= '<input type="checkbox" name="allergia_uova" value="uova" checked/><label> Uova</label><br>';
        } else {
            $allergeniContent .= '<input type="checkbox" name="allergia_uova" value="uova"/><label> Uova</label><br>';
        }

        if ($userPreferences != null && $userPreferences->isAllergeneArachidi()) {
            $allergeniContent .= '<input type="checkbox" name="allergia_arachidi" value="arachidi" checked/><label> Arachidi</label>';
        } else {
            $allergeniContent .= '<input type="checkbox" name="allergia_arachidi" value="arachidi"/><label> Arachidi</label>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "allergeni-options-template",
            $allergeniContent
        );


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

        // ======== Daltonismo =========

        $daltonismoContent = "";

        $opzioniDaltonici = FiltroDaltonici::cases();
        $isDaltonico = false;

        foreach ($opzioniDaltonici as $opzione) {
            if ($userPreferences != null && $userPreferences->getFiltroDaltonici()->value == $opzione->value) {
                $isDaltonico = true;
                $daltonismoContent .= '<option value="' . $opzione->value . '" selected>' . $opzione->value . '</option>';
            } else {
                $daltonismoContent .= '<option value="' . $opzione->value . '">' . $opzione->value . '</option>';
            }
        }



        Utils::replaceTemplateContent(
            $this->dom,
            "daltonismo-options-template",
            $daltonismoContent
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
