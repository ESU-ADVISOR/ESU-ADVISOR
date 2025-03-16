<?php

namespace Views;

// Assicurarsi di importare primo PreferenzeUtenteModel dove sono definiti gli enum
require_once __DIR__ . "/../models/PreferenzeUtenteModel.php";

use Models\DimensioneIcone;
use Models\DimensioneTesto;
use Models\ModificaFont;
use Models\ModificaTema;
use Models\RecensioneModel;
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
        
        // Caricamento mense
        $menseContent = "";
        $mense = MenseModel::findAll();
        
        // Impostazioni utente ed allergie
        $mensaPreferita = null;
        $allergeni = isset($_SESSION["allergeni"]) ? $_SESSION["allergeni"] : [];
        
        // Caricamento preferenze (se l'utente è loggato)
        $userPreferences = null;
        
        if ($isLoggedIn) {
            $user = UserModel::findByUsername($_SESSION["username"]);
            if ($user !== null) {
                $userPreferences = PreferenzeUtenteModel::findByEmail($user->getEmail());
                
                // Determina la mensa preferita dall'utente loggato
                if ($userPreferences && method_exists($userPreferences, 'getMensaPreferita') && $userPreferences->getMensaPreferita()) {
                    $mensaPreferita = $userPreferences->getMensaPreferita();
                }
            }
        } elseif (isset($_SESSION["mensa_preferita"])) {
            // Utente non loggato: usa le preferenze in sessione
            $mensaPreferita = $_SESSION["mensa_preferita"];
        }

        // Popola le opzioni del menu a tendina per le mense
        $hasMensaPreferita = false;
        
        foreach ($mense as $mensa) {
            $selected = ($mensaPreferita === $mensa->getNome()) ? 'selected' : '';
            if ($selected) {
                $hasMensaPreferita = true;
            }
            $menseContent .= '<option value="' . $mensa->getNome() . '" ' . $selected . '>' . $mensa->getNome() . '</option>';
        }
        
        // Se nessuna mensa è selezionata come preferita, aggiungi un'opzione vuota
        if (!$hasMensaPreferita) {
            $menseContent = '<option value="" selected>Seleziona una mensa</option>' . $menseContent;
        }
        
        Utils::replaceTemplateContent(
            $this->dom,
            "mense-options-template",
            $menseContent
        );
        
        // Gestione stato checkbox degli allergeni
        // Trova tutti i checkbox nel DOM
        $allergeniCheckboxes = $this->dom->getElementsByTagName('input');
        foreach ($allergeniCheckboxes as $checkbox) {
            // Verifica se è un checkbox per allergeni
            if ($checkbox->getAttribute('type') === 'checkbox' && strpos($checkbox->getAttribute('id'), 'allergene-') === 0) {
                $allergeneValue = $checkbox->getAttribute('value');
                // Se questo allergene è stato selezionato, imposta il checkbox come checked
                if (in_array($allergeneValue, $allergeni)) {
                    $checkbox->setAttribute('checked', 'checked');
                }
            }
        }
        
        // ======== Renderizzazione sezione account (condizionale) ========
        if ($isLoggedIn) {
            $accountSettingsContent = '
            <section class="mb-4 pb-4 border-b border-color">
                <h2 class="text-lg font-semibold mb-3">Informazioni <span lang="en">account</span></h2>
                <form action="settings.php" id="change_username_form" method="post" class="base-form">
                    <div class="form-group">
                        <label for="username">Modifica <span lang="en">username</span></label>
                        <input
                            type="username"
                            id="new_username"
                            name="new_username"
                            class="form-input"
                            style="
                                background-image: url(&quot;data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M19 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z%22/><path d=%22m3 7 9 6 9-6%22/></svg>&quot;);
                                background-position: 12px center;
                                background-repeat: no-repeat;
                            "
                        />
                        <button type="submit" class="form-button" name="change_username">Salva</button>
                    </div>
                </form>

                <form action="settings.php" id="change_password_form" method="post" class="base-form">
                    <div class="form-group">
                        <label for="old_password"><span lang="en">Password</span> attuale</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            style="
                                background-image: url(&quot;data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%2211%22 width=%2218%22 height=%2211%22 rx=%222%22 ry=%222%22></rect><path d=%22M7 11V7a5 5 0 0 1 10 0v4%22></path></svg>&quot;);
                                background-position: 12px center;
                                background-repeat: no-repeat;
                            "
                        />

                        <label for="password">Modifica <span lang="en">password</span></label>
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="form-input"
                            style="
                                background-image: url(&quot;data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%2211%22 width=%2218%22 height=%2211%22 rx=%222%22 ry=%222%22></rect><path d=%22M7 11V7a5 5 0 0 1 10 0v4%22></path></svg>&quot;);
                                background-position: 12px center;
                                background-repeat: no-repeat;
                            "
                        />

                        <label for="confirm_password">Conferma <span lang="en">password</span></label>
                        <input
                            type="password"
                            id="new_password_confirm"
                            name="new_password_confirm"
                            class="form-input"
                            style="
                                background-image: url(&quot;data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%2211%22 width=%2218%22 height=%2211%22 rx=%222%22 ry=%222%22></rect><path d=%22M7 11V7a5 5 0 0 1 10 0v4%22></path></svg>&quot;);
                                background-position: 12px center;
                                background-repeat: no-repeat;
                            "
                        />

                        <button type="submit" class="form-button" name="change_password">Salva</button>
                    </div>
                </form>

                <div class="form-group">
                    <label for="delete-account">Elimina l\'<span lang="en">account</span></label>
                    <button type="submit" id="delete-account-button" class="danger form-button">
                        Elimina <span lang="en">Account</span>
                    </button>

                    <div class="modal" id="myModal">
                        <div class="modal-content card card-expanded mb-4">
                            <div class="card-content">
                                <h2 class="card-title">
                                    Conferma l\'eliminazione dell\'<span lang="en">account</span>
                                </h2>
                                <button class="close-button" id="close-modal">&times;</button>
                                <p>
                                    Sei sicuro di volere cancellare il tuo <span lang="en">account</span>?
                                    Questa azione è irreversibile.
                                </p>
                                <form id="delete-account-form" method="POST" action="settings.php">
                                    <span class="center">
                                        <button type="submit" name="delete_account" class="confirmation-button">
                                            Sì
                                        </button>
                                        <button class="cancel-button">No</button>
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>';
            
            Utils::replaceTemplateContent(
                $this->dom,
                "account-settings-template",
                $accountSettingsContent
            );
        }

        // ======== Dark Mode =========
        $temaContent = "";
        // Usa un approccio più sicuro per ottenere i valori dell'enum
        try {
            $opzioniTema = ModificaTema::cases();
        } catch (\Throwable $e) {
            // Fallback in caso l'enum non sia disponibile
            $opzioniTema = [
                (object)['value' => 'Chiaro'], 
                (object)['value' => 'Scuro'], 
                (object)['value' => 'Sistema']
            ];
        }
        
        $temaScelto = null;
        
        // Determina il tema scelto
        if ($isLoggedIn && $userPreferences && method_exists($userPreferences, 'getTema') && $userPreferences->getTema()) {
            $tema = $userPreferences->getTema();
            $temaScelto = is_object($tema) ? $tema->value : $tema;
        } elseif (isset($_SESSION["tema"])) {
            $temaScelto = $_SESSION["tema"];
        }
        
        foreach ($opzioniTema as $opzione) {
            $opzioneValue = is_object($opzione) ? $opzione->value : $opzione;
            $selected = ($temaScelto === $opzioneValue) ? 'selected' : '';
            if (empty($selected) && $opzioneValue == "Sistema" && $temaScelto === null) {
                $selected = 'selected'; // Default to sistema
            }
            $temaContent .= '<option value="' . $opzioneValue . '" ' . $selected . '>' . $opzioneValue . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "tema-option-template",
            $temaContent
        );

        // ======== Dimensione Testo =========
        $dimensioneTestoContent = "";
        // Usa un approccio più sicuro per ottenere i valori dell'enum
        try {
            $opzioniDimensioneTesto = DimensioneTesto::cases();
        } catch (\Throwable $e) {
            // Fallback in caso l'enum non sia disponibile
            $opzioniDimensioneTesto = [
                (object)['value' => 'Piccolo'], 
                (object)['value' => 'Medio'], 
                (object)['value' => 'Grande']
            ];
        }
        
        $dimensioneTestoScelta = null;
        
        // Determina la dimensione testo scelta
        if ($isLoggedIn && $userPreferences && method_exists($userPreferences, 'getDimensioneTesto') && $userPreferences->getDimensioneTesto()) {
            $dimensioneTesto = $userPreferences->getDimensioneTesto();
            $dimensioneTestoScelta = is_object($dimensioneTesto) ? $dimensioneTesto->value : $dimensioneTesto;
        } elseif (isset($_SESSION["dimensione_testo"])) {
            $dimensioneTestoScelta = $_SESSION["dimensione_testo"];
        }
        
        foreach ($opzioniDimensioneTesto as $opzione) {
            $opzioneValue = is_object($opzione) ? $opzione->value : $opzione;
            $selected = ($dimensioneTestoScelta === $opzioneValue) ? 'selected' : '';
            if (empty($selected) && $opzioneValue == "Medio" && $dimensioneTestoScelta === null) {
                $selected = 'selected'; // Default to Medio
            }
            $dimensioneTestoContent .= '<option value="' . $opzioneValue . '" ' . $selected . '>' . $opzioneValue . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-testo-options-template",
            $dimensioneTestoContent
        );

        // ======== Dimensione Icone =========
        $dimensioneIconeContent = "";
        // Usa un approccio più sicuro per ottenere i valori dell'enum
        try {
            $opzioniDimensioneIcone = DimensioneIcone::cases();
        } catch (\Throwable $e) {
            // Fallback in caso l'enum non sia disponibile
            $opzioniDimensioneIcone = [
                (object)['value' => 'Piccolo'], 
                (object)['value' => 'Medio'], 
                (object)['value' => 'Grande']
            ];
        }
        
        $dimensioneIconeScelta = null;
        
        // Determina la dimensione icone scelta
        if ($isLoggedIn && $userPreferences && method_exists($userPreferences, 'getDimensioneIcone') && $userPreferences->getDimensioneIcone()) {
            $dimensioneIcone = $userPreferences->getDimensioneIcone();
            $dimensioneIconeScelta = is_object($dimensioneIcone) ? $dimensioneIcone->value : $dimensioneIcone;
        } elseif (isset($_SESSION["dimensione_icone"])) {
            $dimensioneIconeScelta = $_SESSION["dimensione_icone"];
        }
        
        foreach ($opzioniDimensioneIcone as $opzione) {
            $opzioneValue = is_object($opzione) ? $opzione->value : $opzione;
            $selected = ($dimensioneIconeScelta === $opzioneValue) ? 'selected' : '';
            if (empty($selected) && $opzioneValue == "Medio" && $dimensioneIconeScelta === null) {
                $selected = 'selected'; // Default to Medio
            }
            $dimensioneIconeContent .= '<option value="' . $opzioneValue . '" ' . $selected . '>' . $opzioneValue . '</option>';
        }

        Utils::replaceTemplateContent(
            $this->dom,
            "dimensione-icone-options-template",
            $dimensioneIconeContent
        );

        // ======== Modifica font =========
        $fontContent = "";
        // Usa un approccio più sicuro per ottenere i valori dell'enum
        try {
            $opzioniFont = ModificaFont::cases();
        } catch (\Throwable $e) {
            // Fallback in caso l'enum non sia disponibile
            $opzioniFont = [
                (object)['value' => 'Normale'], 
                (object)['value' => 'Dislessia']
            ];
        }
        
        $fontScelto = null;
        
        // Determina il font scelto
        if ($isLoggedIn && $userPreferences && method_exists($userPreferences, 'getModificaFont') && $userPreferences->getModificaFont()) {
            $font = $userPreferences->getModificaFont();
            $fontScelto = is_object($font) ? $font->value : $font;
        } elseif (isset($_SESSION["modifica_font"])) {
            $fontScelto = $_SESSION["modifica_font"];
        }
        
        foreach ($opzioniFont as $opzione) {
            $opzioneValue = is_object($opzione) ? $opzione->value : $opzione;
            $selected = ($fontScelto === $opzioneValue) ? 'selected' : '';
            if (empty($selected) && $opzioneValue == "Normale" && $fontScelto === null) {
                $selected = 'selected'; // Default to Normale
            }
            $fontContent .= '<option value="' . $opzioneValue . '" ' . $selected . '>' . $opzioneValue . '</option>';
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