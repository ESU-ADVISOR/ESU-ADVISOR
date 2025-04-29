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

        //breadcrumbs
        $breadcrumbContent = '<p>Ti trovi in: Impostazioni';
        Utils::replaceTemplateContent(
            $this->dom,
            "breadcrumb-template",
            $breadcrumbContent
        );

        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        $menseContent = "";
        $mense = MenseModel::findAll();

        $mensaPreferita = null;
        // Inizializza gli allergeni dalla sessione prima di tutto
        $allergeni = isset($_SESSION["allergeni"]) && is_array($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];

        $userPreferences = null;
        if ($isLoggedIn) {
            $user = UserModel::findByUsername($_SESSION["username"]);
            if ($user !== null) {
                $userPreferences = PreferenzeUtenteModel::findByUsername($user->getUsername());

                // Determina la mensa preferita e allergeni dall'utente loggato
                if ($userPreferences) {
                    // Recupera allergeni dal DB (hanno priorità sulla sessione per utenti loggati)
                    $dbAllergeni = $userPreferences->getAllergeni();
                    if (!empty($dbAllergeni)) {
                        $allergeni = $dbAllergeni;
                        $_SESSION["allergeni"] = $allergeni; // Sincronizza la sessione con il DB
                    }
                    $mensaPreferita = $userPreferences->getMensaPreferita();
                }
            }
        }
        
        // Recupera mensa preferita dalla sessione per utenti non loggati
        if (!$isLoggedIn && isset($_SESSION["mensa_preferita"])) {
            $mensaPreferita = $_SESSION["mensa_preferita"];
        }

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
                if (in_array($allergeneValueNormalized, $allergeni, true) || 
                    in_array($allergeneValue, $allergeni, true)) { // Check both normalized and original value
                    $checkbox->setAttribute('checked', 'checked');
                } else {
                    if ($checkbox->hasAttribute('checked')) {
                        $checkbox->removeAttribute('checked');
                    }
                }
            }
        }

        // ======== Renderizzazione sezione account (condizionale) ========
        if ($isLoggedIn) {
            $settingsAccountContent = file_get_contents(__DIR__ . '/../templates/settings-account.html');

            Utils::replaceTemplateContent(
                $this->dom,
                "settings-account-template",
                $settingsAccountContent
            );
        }

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

        // ======== Dimensione Testo =========
        $dimensioneTestoContent = "";
        $opzioniDimensioneTesto = DimensioneTesto::cases();
        $dimensioneTestoScelta = null;

        if ($userPreferences != null && $userPreferences->getDimensioneTesto()) {
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

        // ======== Dimensione Icone =========
        $dimensioneIconeContent = "";
        $opzioniDimensioneIcone = DimensioneIcone::cases();
        $dimensioneIconeScelta = null;

        if ($userPreferences != null && $userPreferences->getDimensioneIcone()) {
            $dimensioneIconeScelta = $userPreferences->getDimensioneIcone()->value;
        } elseif (isset($_SESSION["dimensione_icone"])) {
            $dimensioneIconeScelta = $_SESSION["dimensione_icone"];
        }

        foreach ($opzioniDimensioneIcone as $opzione) {
            if ($dimensioneIconeScelta != null) {
                $selected = ($dimensioneIconeScelta === $opzione->value) ? 'selected' : '';
            } else {
                $selected = $opzione->value == "medio" ? 'selected' : '';
            }
            $dimensioneIconeContent .= '<option value="' . $opzione->value . '" ' . $selected . '>' . ucwords($opzione->value) . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-icone-options-template",
            $dimensioneIconeContent
        );

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

        // ======== Messaggi di errore o successo =========
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
