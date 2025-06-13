<?php

namespace Views;

use Models\Enums\DimensioneTesto;
use Models\Enums\DimensioneIcone;
use Models\Enums\ModificaFont;
use Models\Enums\ModificaTema;
use Models\UserModel;
use Models\MenseModel;
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

        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        $userPreferences = null;
        if ($isLoggedIn) {
            $user = UserModel::findByUsername($_SESSION["username"]);
            if ($user !== null) {
                $userPreferences = PreferenzeUtenteModel::findByUsername($user->getUsername());


                if ($userPreferences) {

                    $dbAllergeni = $userPreferences->getAllergeni();
                    if (!empty($dbAllergeni)) {
                        $allergeni = $dbAllergeni;
                        $_SESSION["allergeni"] = $allergeni;
                    }
                    $mensaPreferita = $userPreferences->getMensaPreferita();
                }
            }
        }

        $this->renderGeneralPreferences();

        $this->renderFontPreferences($userPreferences);
        $this->renderTextSizePreferences($userPreferences);
        $this->renderThemePreferences($userPreferences);

        if ($isLoggedIn) {
            $this->renderAccountSection($data);
        }

        $template_id = "server-response-preferences-template";

        if (isset($data["type"])) {
            print_r($data["type"]);
            if ($data["type"] == "username_change") {
                $template_id = "server-response-username-template";
            } elseif ($data["type"] == "password_change") {
                $template_id = "server-response-password-template";
            } elseif ($data["type"] == "accessibility_change") {
                $template_id = "server-response-accessibility-template";
            }
        }

        // ======== Messaggi di errore o successo =========
        if (isset($data["errors"])) {
            $errorHtml = "";
            foreach ($data["errors"] as $error) {
                $errorHtml .= "<div class='error'>$error</div>";
            }
            Utils::replaceTemplateContent(
                $this->dom,
                $template_id,
                $errorHtml
            );
        }

        if (isset($data["success"])) {
            $successHtml = "<div class='success'>{$data["success"]}</div>";

            Utils::replaceTemplateContent(
                $this->dom,
                $template_id,
                $successHtml
            );
        }

        echo $this->dom->saveHTML();
    }


    private function renderGeneralPreferences(): void
    {
        $menseContent = "";
        $mense = MenseModel::findAll();

        $mensaPreferita = isset($_SESSION["mensa_preferita"]) ? $_SESSION["mensa_preferita"] : null;

        $allergeni = isset($_SESSION["allergeni"]) && is_array($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];

        $hasMensaPreferita = false;
        foreach ($mense as $mensa) {
            $selected = ($mensaPreferita === $mensa->getNome()) ? 'selected' : '';
            if ($selected) {
                $hasMensaPreferita = true;
            }
            $menseContent .= '<option value="' . $mensa->getNome() . '" ' . $selected . '>' . $mensa->getNome() . '</option>';
        }

        if (!$hasMensaPreferita) {
            $menseContent = '<option value="" selected>Seleziona una mensa</option>' . $menseContent;
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "mense-options-template",
            $menseContent
        );


        $allergeniCheckboxes = $this->dom->getElementsByTagName('input');
        foreach ($allergeniCheckboxes as $checkbox) {
            if ($checkbox->getAttribute('type') === 'checkbox' && $checkbox->getAttribute('name') === 'allergeni[]') {
                $allergeneValue = $checkbox->getAttribute('value');
                // Ensure consistent casing for comparison
                $allergeneValueNormalized = ucfirst($allergeneValue);
                if (
                    in_array($allergeneValueNormalized, $allergeni, true) ||
                    in_array($allergeneValue, $allergeni, true)
                ) { // Check both normalized and original value
                    $checkbox->setAttribute('checked', 'checked');
                } else {
                    if ($checkbox->hasAttribute('checked')) {
                        $checkbox->removeAttribute('checked');
                    }
                }
            }
        }
    }

    private function renderAccountSection($data): void
    {
        $settingsAccountContent = file_get_contents(__DIR__ . '/../templates/settings-account.html');

        Utils::replaceTemplateContent(
            $this->dom,
            "settings-account-template",
            $settingsAccountContent
        );

        // ricompilazione campi se si proviene da un errore
        if (isset($data["formData"])) {
            $formData = $data["formData"];

            if (isset($formData["new_username"]) && !empty($formData["new_username"])) {
                $this->dom->getElementById("new_username")->setAttribute("value", htmlspecialchars($formData["new_username"]));
            }

            if (isset($formData["password"]) && !empty($formData["password"])) {
                $this->dom->getElementById("password")->setAttribute("value", htmlspecialchars($formData["password"]));
            }

            if (isset($formData["new_password"]) && !empty($formData["new_password"])) {
                $this->dom->getElementById("new_password")->setAttribute("value", htmlspecialchars($formData["new_password"]));
            }

            if (isset($formData["new_password_confirm"]) && !empty($formData["new_password_confirm"])) {
                $this->dom->getElementById("new_password_confirm")->setAttribute("value", htmlspecialchars($formData["new_password_confirm"]));
            }
        }
    }

    private function renderThemePreferences($userPreferences): void
    {
        // ======== Dark Mode =========
        $temaContent = "";
        $opzioniTema = ModificaTema::cases();
        $temaScelto = null;

        if ($userPreferences != null && $userPreferences->getTema() != null) {
            $temaScelto = $userPreferences->getTema()->value;
        } elseif (isset($_SESSION["tema"])) {
            $temaScelto = $_SESSION["tema"];
        }

        foreach ($opzioniTema as $opzione) {
            if ($temaScelto != null) {
                $selected = ($temaScelto === $opzione->value) ? 'selected' : '';
            } else {
                $selected = $opzione->value == "sistema" ? 'selected' : '';
            }
            $temaContent .= '<option value="' . $opzione->value . '" ' . $selected . '>' . ucwords($opzione->value) . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "tema-option-template",
            $temaContent
        );
    }

    private function renderFontPreferences($userPreferences): void
    {

        // ======== Modifica font =========
        $fontContent = "";
        $opzioniFont = ModificaFont::cases();
        $fontScelto = null;

        if ($userPreferences != null && $userPreferences->getModificaFont()) {
            $fontScelto = $userPreferences->getModificaFont()->value;
        } elseif (isset($_SESSION["modifica_font"])) {
            $fontScelto = $_SESSION["modifica_font"];
        }

        foreach ($opzioniFont as $opzione) {
            if ($fontScelto != null) {
                $selected = ($fontScelto === $opzione->value) ? 'selected' : '';
            } else {
                $selected = $opzione->value == "sistema" ? 'selected' : '';
            }
            $fontContent .= '<option value="' . $opzione->value . '" ' . $selected . '>' . ucwords($opzione->value) . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "font-options-template",
            $fontContent
        );
    }

    private function renderTextSizePreferences($userPreferences): void
    {
        // ======== Dimensione Testo =========
        $dimensioneTestoContent = "";
        $opzioniDimensioneTesto = DimensioneTesto::cases();
        $dimensioneTestoScelta = null;

        if (isset($userPreferences)) {
            $dimensioneTestoScelta = $userPreferences->getDimensioneTesto()->value;
        } elseif (isset($_SESSION["dimensione_testo"])) {
            $dimensioneTestoScelta = $_SESSION["dimensione_testo"];
        }

        foreach ($opzioniDimensioneTesto as $opzione) {
            if ($dimensioneTestoScelta != null) {
                $selected = ($dimensioneTestoScelta === $opzione->value) ? 'selected' : '';
            } else {
                $selected = $opzione->value == "medio" ? 'selected' : '';
            }
            $dimensioneTestoContent .= '<option value="' . $opzione->value . '" ' . $selected . '>' . ucwords($opzione->value) . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-testo-options-template",
            $dimensioneTestoContent
        );
    }
}
