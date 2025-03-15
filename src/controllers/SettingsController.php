<?php

namespace Controllers;

use Models\ModificaTema;
use Models\DimensioneIcone;
use Models\DimensioneTesto;
use Models\FiltroDaltonici;
use Models\ModificaFont;
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
        } else if (isset($post['preferences'])) {
            $email = UserModel::findByUsername($_SESSION["username"])->getEmail();

            $preferences = PreferenzeUtenteModel::findByEmail($email) ?? new PreferenzeUtenteModel();

            if (isset($post['modifica_tema']) && $post['modifica_tema'] !== "none") {
                $preferences->setTema(ModificaTema::tryFrom($post['modifica_tema']));
            } else {
                $preferences->setTema(ModificaTema::SISTEMA);
            }
            if (isset($post['dimensione_testo']) && $post['dimensione_testo'] !== "none") {
                $preferences->setDimensioneTesto(DimensioneTesto::tryFrom($post['dimensione_testo']));
            } else {
                $preferences->setDimensioneTesto(DimensioneTesto::MEDIO);
            }
            if (isset($post['dimensione_icone']) && $post['dimensione_icone'] !== "none") {
                $preferences->setDimensioneIcone(DimensioneIcone::tryFrom($post['dimensione_icone']));
            } else {
                $preferences->setDimensioneIcone(DimensioneIcone::MEDIO);
            }
            if (isset($post['modifica_font']) && $post['modifica_font'] !== "none") {
                $preferences->setModificaFont(ModificaFont::tryFrom($post['modifica_font']));
            } else {
                $preferences->setModificaFont(ModificaFont::NORMALE);
            }
            $preferences->setEmail($email);

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
        } else if (isset($post['change_username'])) {
            print_r($post);

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

            if (!UserModel::authenticate($user->getEmail(), $post['password'])) {
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
