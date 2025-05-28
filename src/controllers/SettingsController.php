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
            ]);
            exit();
        }

        if ($user->deleteFromDB()) {
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            $view->render([
                "errors" => ["Eliminazione account fallita: impossibile rimuovere dal database"],
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
            ]);
            exit();
        }

        if (empty($post['new_username'])) {
            $view->render([
                "errors" => ["Username non può essere vuoto"],
                "formData" => $post
            ]);
            exit();
        } else {
            if (strlen($post['new_username']) < 3 || strlen($post['new_username']) > 50) {
                $view->render([
                    "errors" => ["L'username deve essere compreso tra 3 e 50 caratteri"],
                    "formData" => $post
                ]);
                exit();
            }
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $post['new_username'])) {
                $view->render([
                    "errors" => ["L'username può contenere solo lettere, numeri, underscore e trattini"],
                    "formData" => $post
                ]);
                exit();
            }
        }

        $user->setUsername($post['new_username']);

        if ($user->saveToDB()) {
            $_SESSION["username"] = $user->getUsername();
            $view->render([
                "success" => "Username modificato con successo",
            ]);
            exit();
        } else {
            $view->render([
                "errors" => ["Modifica username non riuscita"],
                "formData" => $post
            ]);
            exit();
        }
    }

    private function changePassword(array $post = []): void
    {
        $view = new SettingsView();
        $user = UserModel::findByUsername($_SESSION["username"]);
        if ($user === null) {
            $view->render([
                "errors" => ["Utente non trovato"],
            ]);
            exit();
        }

        if (!UserModel::authenticate($user->getUsername(), $post['password'])) {
            $view->render([
                "errors" => ["La password attuale è errata"],
                "formData" => [
                    "new_password" => $post['new_password'],
                    "new_password_confirm" => $post['new_password_confirm']
                ]
            ]);
            exit();
        }

        if ($post['new_password'] !== $post['new_password_confirm']) {
            $view->render([
                "errors" => ["Le nuove password non corrispondono"],
                "formData" => []
            ]);
            exit();
        }

        if ($post['new_password'] === $post['password']) {
            $view->render([
                "errors" => ["La nuova password deve essere diversa da quella attuale"],
                "formData" => []
            ]);
            exit();
        }

        $user->setClearPassword($post['new_password']);

        if ($user->saveToDB()) {
            $view->render([
                "success" => "Password modificata con successo"
            ]);
            exit();
        } else {
            $view->render([
                "errors" => ["Modifica password non riuscita"],
                "formData" => []
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
                    "errors" => ["Mensa non trovata nel database"],
                    "formData" => $post
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
            $username = UserModel::findByUsername($_SESSION["username"])->getUsername();
            $preferences = PreferenzeUtenteModel::findByUsername($username) ?? new PreferenzeUtenteModel();

            $preferences->setUsername($username);
            if ($mensaPreferita) {
                $preferences->setMensaPreferita($mensaPreferita);
                $_SESSION["mensa_preferita"] = $mensaPreferita;
            }

            try {
                if (!$preferences->saveToDB()) {
                    throw new \Exception("Impossibile salvare le preferenze");
                }

                $preferences->saveAllergeni($allergeni);
                
                $view->render([
                    "success" => "Preferenze salvate con successo",
                ]);
                exit();
                
            } catch (\Exception $e) {
                error_log("Errore nel salvataggio preferenze: " . $e->getMessage());
                
                $view->render([
                    "errors" => ["Impossibile salvare le preferenze: " . $e->getMessage()],
                    "formData" => $post
                ]);
                exit();
            }
        } else {
            if ($mensaPreferita) {
                $_SESSION["mensa_preferita"] = $mensaPreferita;
            }
            
            $view->render([
                "success" => "Preferenze salvate per questa sessione",
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

            $dimensioneIcone = isset($post['dimensione_icone']) && $post['dimensione_icone'] !== "none" ?
                DimensioneIcone::tryFrom($post['dimensione_icone']) : DimensioneIcone::MEDIO;

            $font = isset($post['modifica_font']) && $post['modifica_font'] !== "none" ?
                ModificaFont::tryFrom($post['modifica_font']) : ModificaFont::NORMALE;

            $_SESSION["tema"] = $tema->value;
            $_SESSION["dimensione_testo"] = $dimensioneTesto->value;
            $_SESSION["dimensione_icone"] = $dimensioneIcone->value;
            $_SESSION["modifica_font"] = $font->value;

                
            if ($isLoggedIn) {
                $username = UserModel::findByUsername($_SESSION["username"])->getUsername();
                $preferences = PreferenzeUtenteModel::findByUsername($username) ?? new PreferenzeUtenteModel();

                $preferences->setUsername($username);
                $preferences->setTema($tema);
                $preferences->setDimensioneTesto($dimensioneTesto);
                $preferences->setDimensioneIcone($dimensioneIcone);
                $preferences->setModificaFont($font);

                if ($preferences->saveToDB()) {
                    $view->render([
                        "success" => "Preferenze salvate con successo",
                    ]);
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Impossibile salvare le preferenze"],
                        "formData" => $post
                    ]);
                    exit();
                }
            } else {
                $view->render([
                    "success" => "Preferenze salvate per questa sessione",
                ]);
            }
        } catch (\Exception $e) {
            error_log("Errore nel salvataggio preferenze: " . $e->getMessage());
            
            $view->render([
                "errors" => ["Impossibile salvare le preferenze: " . $e->getMessage()],
                "formData" => $post
            ]);
            exit();
        }
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new SettingsView();
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