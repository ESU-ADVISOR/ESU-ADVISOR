<?php

namespace Controllers;

use Models\Enums\ModificaTema;
use Models\Enums\DimensioneTesto;
use Models\Enums\DimensioneIcone;
use Models\Enums\ModificaFont;
use Models\MenseModel;
use Models\PreferenzeUtenteModel;
use Models\UserModel;
use Views\SettingsView;

class SettingsController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new SettingsView();
        $view->render($get);
    }

    private function deleteAccount(array $post = []): void
    {
        $view = new SettingsView();
        $user = UserModel::findByUsername($_SESSION["username"]);
        if ($user === null) {
            $view->render([
                "errors" => ["Utente non trovato"],
                "template" => "server-response-delete-account-template"
            ]);
            exit();
        }

        if ($user->deleteFromDB()) {
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            $view->render([
                "errors" => ["Eliminazione account fallita: impossibile rimuovere dal <span lang='en'>database</span>"],
                "template" => "server-response-delete-account-template"
            ]);
            exit();
        }
    }

    private function changeUsername(array $post = []): void
    {
        $view = new SettingsView();
        $user = UserModel::findByUsername($_SESSION["username"]);
        if ($user === null) {
            $view->render([
                "errors" => ["Utente non trovato"],
                "template" => "server-response-username-template"
            ]);
            exit();
        }

        $newUser = UserModel::findByUsername($post['new_username']);
        if ($newUser !== null) {
            $view->render([
                "errors" => ["Lo <span lang='en'>username</span> scelto è già in uso da un altro utente"],
                "formData" => ['new_username' => $post['new_username']],
                "template" => "server-response-username-template"
            ]);
            exit();
        }

        if (empty($post['new_username'])) {
            $view->render([
                "errors" => ["<span lang='en'>Username</span> non può essere vuoto"],
                "formData" => ['new_username' => $post['new_username']],
                "template" => "server-response-username-template"
            ]);
            exit();
        } else {
            if (strlen($post['new_username']) < 3 || strlen($post['new_username']) > 50) {
                $view->render([
                    "errors" => ["Lo <span lang='en'>username</span> deve essere compreso tra 3 e 50 caratteri"],
                    "formData" => ['new_username' => $post['new_username']],
                    "template" => "server-response-username-template"
                ]);
                exit();
            }
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $post['new_username'])) {
                $view->render([
                    "errors" => ["Lo <span lang='en'>username</span> può contenere solo lettere, numeri, <span lang='en'>underscore</span> e trattini"],
                    "formData" => ['new_username' => $post['new_username']],
                    "template" => "server-response-username-template"
                ]);
                exit();
            }
        }

        $user->setUsername($post['new_username']);

        if ($user->saveToDB()) {
            $_SESSION["username"] = $user->getUsername();
            $view->render([
                "success" => "<span lang='en'>Username</span> modificato con successo",
                "template" => "server-response-username-template"
            ]);
            exit();
        } else {
            $view->render([
                "errors" => ["Modifica <span lang='en'>username</span> non riuscita"],
                "formData" => ['new_username' => $post['new_username']],
                "template" => "server-response-username-template"
            ]);
            exit();
        }
    }

    private function changePassword(array $post = []): void
    {
        $view = new SettingsView();
        $user = UserModel::findByUsername($_SESSION["username"]);
        $errors = [];

        if ($user === null) {
            $view->render([
                "errors" => ["Utente non trovato"],
                "template" => "server-response-password-template"
            ]);
            exit();
        }

        if (!UserModel::authenticate($user->getUsername(), $post['current_password'])) {
            $errors[] = "La <span lang='en'>password</span> attuale è errata";
        }

        if ($post['new_password'] !== $post['new_password_confirm']) {
            $errors[] = "Le nuove <span lang='en'>password</span> non corrispondono";
        }

        if ($post['new_password'] === $post['current_password']) {
            $errors[] = "La nuova <span lang='en'>password</span> deve essere diversa da quella attuale";
        }

        if (strlen($post['new_password']) < 8) {
            $errors[] = "La <span lang='en'>password</span> deve essere di almeno 8 caratteri";
        }

        if (
            !preg_match(
                '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                $post['new_password']
            )
        ) {
            $errors[] =
                "La <span lang='en'>password</span> deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&).";
        }

        if (!empty($errors)) {
            $view->render([
                "errors" => $errors,
                "formData" => [
                    "new_password" => $post['new_password'],
                    "new_password_confirm" => $post['new_password_confirm']
                ],
                "template" => "server-response-password-template"
            ]);
            exit();
        }

        $user->setClearPassword($post['new_password']);

        if ($user->saveToDB()) {
            $view->render([
                "success" => "<span lang='en'>Password</span> modificata con successo",
                "template" => "server-response-password-template"
            ]);
            exit();
        } else {
            $view->render([
                "errors" => ["Modifica <span lang='en'>password</span> non riuscita"],
                "formData" => [],
                "template" => "server-response-password-template"
            ]);
            exit();
        }
    }

    private function savePreferences(array $post = []): void
    {
        $view = new SettingsView();
        $mensaPreferita = null;
        if (isset($post['mensa_preferita']) && !empty($post['mensa_preferita'])) {
            $mensa = MenseModel::findByName($post['mensa_preferita']);
            if ($mensa === null) {
                $view->render([
                    "errors" => ["Mensa non trovata nel <span lang='en'>database</span>"],
                    "formData" => $post,
                    "template" => "server-response-preferences-template"
                ]);
                exit();
            }
            $mensaPreferita = $post['mensa_preferita'];
        }

        $allergeni = [];
        if (isset($post['allergeni']) && is_array($post['allergeni'])) {
            $allergeni = $post['allergeni'];
        }

        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        if (!empty($allergeni)) {
            $allergeni = array_map('ucfirst', $allergeni);
        } else {
            $allergeni = [];
        }

        $_SESSION["allergeni"] = $allergeni;

        if ($isLoggedIn) {
            $idUtente = UserModel::findByUsername($_SESSION["username"])->getId();
            $preferences = PreferenzeUtenteModel::findByUsername($_SESSION["username"]);

            $preferences->setIdUtente($idUtente);
            if ($mensaPreferita) {
                $preferences->setMensaPreferita($mensaPreferita);
                $_SESSION["mensa_preferita"] = $mensaPreferita;
            }

            try {
                if (!$preferences->saveToDB()) {
                    throw new \Exception("Impossibile salvare le preferenze della mensa");
                }

                $preferences->saveAllergeni($allergeni);

                $view->render([
                    "success" => "Preferenze salvate con successo",
                    "template" => "server-response-preferences-template"
                ]);
                exit();
            } catch (\Exception $e) {
                error_log("Errore nel salvataggio preferenze: " . $e->getMessage());

                $view->render([
                    "errors" => ["Impossibile salvare le preferenze della mensa: " . $e->getMessage()],
                    "formData" => $post,
                    "template" => "server-response-preferences-template"
                ]);
                exit();
            }
        } else {
            if ($mensaPreferita) {
                $_SESSION["mensa_preferita"] = $mensaPreferita;
            }

            $view->render([
                "success" => "Preferenze salvate per questa sessione",
                "template" => "server-response-preferences-template"
            ]);
            exit();
        }
    }

    private function saveUserPreferences(array $post = []): void
    {
        $view = new SettingsView();
        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        try {
            $tema = isset($post['modifica_tema']) && $post['modifica_tema'] !== "none" ?
                ModificaTema::tryFrom($post['modifica_tema']) : ModificaTema::SISTEMA;

            $dimensioneTesto = isset($post['dimensione_testo']) && $post['dimensione_testo'] !== "none" ?
                DimensioneTesto::tryFrom($post['dimensione_testo']) : DimensioneTesto::MEDIO;


            $font = isset($post['modifica_font']) && $post['modifica_font'] !== "none" ?
                ModificaFont::tryFrom($post['modifica_font']) : ModificaFont::NORMALE;

            $_SESSION["tema"] = $tema->value;
            $_SESSION["dimensione_testo"] = $dimensioneTesto->value;
            $_SESSION["modifica_font"] = $font->value;


            if ($isLoggedIn) {
                $idUtente = UserModel::findByUsername($_SESSION["username"])->getId();
                $preferences = PreferenzeUtenteModel::findByUsername($_SESSION["username"]) ?? new PreferenzeUtenteModel();

                $preferences->setIdUtente($idUtente);
                $preferences->setTema($tema);
                $preferences->setDimensioneTesto($dimensioneTesto);
                $preferences->setModificaFont($font);

                $preferences->syncToSession();

                if ($preferences->saveToDB()) {
                    $view->render([
                        "success" => "Preferenze salvate con successo",
                        "template" => "server-response-accessibility-template"
                    ]);
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Impossibile salvare le preferenze dell'utente"],
                        "formData" => $post,
                        "template" => "server-response-accessibility-template"
                    ]);
                    exit();
                }
            } else {
                $view->render([
                    "success" => "Preferenze salvate per questa sessione",
                    "template" => "server-response-accessibility-template"
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore nel salvataggio preferenze: " . $e->getMessage());

            $view->render([
                "errors" => ["Impossibile salvare le preferenze dell'utente: " . $e->getMessage()],
                "formData" => $post,
                "template" => "server-response-accessibility-template"
            ]);
            exit();
        }
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);

        if ($isLoggedIn) {
            if (isset($post['delete_account'])) {
                $this->deleteAccount($post);
            } else if (isset($post['change_username'])) {
                $this->changeUsername($post);
            } else if (isset($post['change_password'])) {
                $this->changePassword($post);
            }
        }

        if (isset($post['save_preferenze_generali']))
            $this->savePreferences($post);
        else if (isset($post['preferences'])) {
            $this->saveUserPreferences($post);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "error" => "Richiesta POST non consentita",
            ]);
            exit();
        }
    }
}
