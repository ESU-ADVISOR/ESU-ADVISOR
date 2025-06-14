<?php

namespace Views;

use Views\Utils;

class RegisterView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/register.html");
    }

    public function render(array $data = []): void
    {
        parent::render();

        if (isset($data["errors"])) {
            $errorHtml = "";
            foreach ($data["errors"] as $error) {
                $errorHtml .= "<div class='error'>$error</div>";
            }
            $errorHtml = "<div class='error-container' role='alert' aria-live='assertive'>$errorHtml</div>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $errorHtml
            );
        }

        if (isset($data["success"])) {
            $successHtml = "<p class='success' role='polite' aria-live='region'>{$data["success"]} Ora puoi effettuare il <a href='login.php' lang='en'>login</a></p>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        }

        if (isset($data["formData"])) {

            if (isset($data["formData"]["username"])) {
                $username = htmlspecialchars($data["formData"]["username"]);
                $this->dom->getElementById("username")->setAttribute(
                    "value",
                    $username
                );
            }
            if (isset($data["formData"]["birth_date"])) {
                $birthDate = htmlspecialchars($data["formData"]["birth_date"]);
                $this->dom->getElementById("birth_date")->setAttribute(
                    "value",
                    $birthDate
                );
            }
            if (isset($data["formData"]["password"])) {
                $password = htmlspecialchars($data["formData"]["password"]);
                $this->dom->getElementById("password")->setAttribute(
                    "value",
                    $password
                );
            }
            if (isset($data["formData"]["confirm_password"])) {
                $confirmPassword = htmlspecialchars($data["formData"]["confirm_password"]);
                $this->dom->getElementById("confirm_password")->setAttribute(
                    "value",
                    $confirmPassword
                );
            }
        }

        if (isset($data["focus"])) {
            $focusElement = $this->dom->getElementById($data["focus"]);
            if ($focusElement) {
                $focusElement->setAttribute("autofocus", "autofocus");
            }
        }

        echo $this->dom->saveHTML();
    }
}
