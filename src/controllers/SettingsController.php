<?php

namespace Controllers;

use Models\ModificaTema;
use Models\DimensioneIcone;
use Models\DimensioneTesto;
use Models\ModificaFont;
use Models\MenseModel;
use Models\PreferenzeUtenteModel;
use Models\UserModel;
use Views\SettingsView;

class SettingsController implements BaseController
{
    public function handleGETRequest(array $get = []): void
    {
        $view = new \Views\SettingsView();
        $view->render($get);
    }

    public function handlePOSTRequest(array $post = []): void
    {
        $view = new SettingsView();
        $isLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]);
        
        // Gestione account (richiede login)
        if ($isLoggedIn) {
            if (isset($post['delete_account'])) {
                $user = UserModel::findByUsername($_SESSION["username"]);
                if ($user === null) {
                    $view->render([
                        "errors" => ["User not found"],
                    ]);
                    exit();
                }

                if ($user->deleteFromDB()) {
                    session_destroy();
                    header("Location: index.php");
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Registration failed: Could not remove from the database"],
                    ]);
                    exit();
                }
            } else if (isset($post['change_username'])) {
                $user = UserModel::findByUsername($_SESSION["username"]);
                if ($user === null) {
                    $view->render([
                        "errors" => ["User not found"],
                    ]);
                    exit();
                }

                if (empty($post['new_username'])) {
                    $view->render([
                        "success" => "Username changed successfully",
                    ]);
                    exit();
                } else {
                    if (strlen($post['new_username']) < 3 || strlen($post['new_username']) > 50) {
                        $view->render([
                            "errors" => ["Username must be between 3 and 50 characters long."],
                        ]);
                        exit();
                    }
                    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $post['new_username'])) {
                        $view->render([
                            "errors" => ["Username can only contain letters, numbers, underscores, and hyphens."],
                        ]);
                        exit();
                    }
                }

                $user->setUsername($post['new_username']);

                if ($user->saveToDB()) {
                    $view->render([
                        "success" => "Username changed successfully",
                    ]);
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Username not changed"],
                    ]);
                    exit();
                }
            } else if (isset($post['change_password'])) {
                $user = UserModel::findByUsername($_SESSION["username"]);
                if ($user === null) {
                    $view->render([
                        "errors" => ["User not found"],
                    ]);
                    exit();
                }

                if (!UserModel::authenticate($user->getUsername(), $post['password'])) {
                    $view->render([
                        "errors" => ["Old password is incorrect"],
                    ]);
                    exit();
                }

                if ($post['new_password'] !== $post['new_password_confirm']) {
                    $view->render([
                        "errors" => ["New passwords do not match"],
                    ]);
                    exit();
                }

                if ($post['new_password'] === $post['password']) {
                    $view->render([
                        "errors" => ["New password must be different from the old one"],
                    ]);
                    exit();
                }

                $user->setClearPassword($post['new_password']);

                if ($user->saveToDB()) {
                    $view->render([
                        "success" => "Password changed successfully"
                    ]);
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Password not changed"],
                    ]);
                    exit();
                }
            }
        }
        
        // Gestione preferenze generali (accessibile a tutti)
        if (isset($post['save_preferenze_generali'])) {
            // Gestione della mensa preferita
            $mensaPreferita = null;
            if (isset($post['mensa_preferita']) && !empty($post['mensa_preferita'])) {
                // Verifica se la mensa esiste
                $mensa = MenseModel::findByName($post['mensa_preferita']);
                if ($mensa === null) {
                    $view->render([
                        "errors" => ["Mensa non trovata nel database"],
                    ]);
                    exit();
                }
                $mensaPreferita = $post['mensa_preferita'];
            }
            
            // Gestione degli allergeni
            $allergeni = [];
            if (isset($post['allergeni']) && is_array($post['allergeni'])) {
                $allergeni = $post['allergeni'];
            }
            
            // Se l'utente è loggato
            if ($isLoggedIn) {
                $username = UserModel::findByUsername($_SESSION["username"])->getUsername();
                $preferences = PreferenzeUtenteModel::findByUsername($username) ?? new PreferenzeUtenteModel();
                
                // Aggiorna o imposta le preferenze
                $preferences->setUsername($username);
                if ($mensaPreferita) {
                    $preferences->setMensaPreferita($mensaPreferita);
                }
                
                // Gestione degli allergeni nel database - da implementare con una tabella separata
                // per ora lo memorizziamo solo in sessione
                $_SESSION["allergeni"] = $allergeni;
                
                if ($preferences->saveToDB()) {
                    $view->render([
                        "success" => "Preferenze salvate con successo",
                    ]);
                    exit();
                } else {
                    $view->render([
                        "errors" => ["Impossibile salvare le preferenze"],
                    ]);
                    exit();
                }
            } else {
                // Se l'utente non è loggato, salva le preferenze nella sessione
                if ($mensaPreferita) {
                    $_SESSION["mensa_preferita"] = $mensaPreferita;
                }
                $_SESSION["allergeni"] = $allergeni;
                
                $view->render([
                    "success" => "Preferenze salvate per questa sessione",
                ]);
                exit();
            }
        } else if (isset($post['preferences'])) {
            // Elaborazione delle preferenze di accessibilità
            $tema = isset($post['modifica_tema']) && $post['modifica_tema'] !== "none" ? 
                ModificaTema::tryFrom($post['modifica_tema']) : ModificaTema::SISTEMA;
            
            $dimensioneTesto = isset($post['dimensione_testo']) && $post['dimensione_testo'] !== "none" ? 
                DimensioneTesto::tryFrom($post['dimensione_testo']) : DimensioneTesto::MEDIO;
            
            $dimensioneIcone = isset($post['dimensione_icone']) && $post['dimensione_icone'] !== "none" ? 
                DimensioneIcone::tryFrom($post['dimensione_icone']) : DimensioneIcone::MEDIO;
            
            $font = isset($post['modifica_font']) && $post['modifica_font'] !== "none" ? 
                ModificaFont::tryFrom($post['modifica_font']) : ModificaFont::NORMALE;
            
            // Memorizza sempre in sessione
            $_SESSION["tema"] = $tema->value;
            $_SESSION["dimensione_testo"] = $dimensioneTesto->value;
            $_SESSION["dimensione_icone"] = $dimensioneIcone->value;
            $_SESSION["modifica_font"] = $font->value;
            
            // Se l'utente è loggato, salva anche nel database
            if ($isLoggedIn) {
                $username = UserModel::findByUsername($_SESSION["username"])->getUsername();
                $preferences = PreferenzeUtenteModel::findByUsername($username) ?? new PreferenzeUtenteModel();
                
                $preferences->setUsername($username);
                $preferences->setTema($tema);
                $preferences->setDimensioneTesto($dimensioneTesto);
                $preferences->setDimensioneIcone($dimensioneIcone);
                $preferences->setModificaFont($font);
                
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

                if ($preferences->saveToDB()) {
                    // Eccezione per evitare il flash del tema opposto
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Preferenze salvate con successo'
                        ]);
                    } else {
                        // Fallback
                        $view->render([
                            "success" => "Preferenze salvate con successo",
                        ]);
                    }
                    exit();
                } else {
                    // Eccezione per evitare il flash del tema opposto
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Impossibile salvare le preferenze'
                        ]);
                    } else {
                        $view->render([
                            "errors" => ["Impossibile salvare le preferenze"],
                        ]);
                    }
                    exit();
                }
            } else {
                // Utente non loggato
                $view->render([
                    "success" => "Preferenze salvate per questa sessione",
                ]);
                exit();
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "error" => "POST request not allowed",
            ]);
            exit();
        }
    }
}