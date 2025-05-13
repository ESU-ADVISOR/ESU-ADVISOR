<?php

namespace Views;

use Views\Utils;

class LoginView extends BaseView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../templates/login.html");
    }

    public function render(array $data = []): void
    {
        parent::render();
        parent::render();
        $breadcrumbContent = '<h1 lang="en" >Ti trovi in: Login</h1>';
        Utils::replaceTemplateContent(
            $this->dom,
            "breadcrumb-template",
            $breadcrumbContent
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

        // Aggiunta della persistenza dei dati del form
        if (isset($data["formData"])) {
            $formData = $data["formData"];
            $html = $this->dom->saveHTML();

            // Ripopola il campo username
            if (isset($formData["username"])) {
                $pattern = '/<input[^>]*id="username"[^>]*>/';
                $replacement = '<input type="text" id="username" name="username" required class="form-input" value="' .
                    htmlspecialchars($formData["username"]) . '" style="background-image: url(&quot;data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2%22></path><circle cx=%2212%22 cy=%227%22 r=%224%22></circle></svg>&quot;); background-position: 12px center; background-repeat: no-repeat;"';
                $html = preg_replace($pattern, $replacement, $html);
            }

            echo $html;
            return;
        }

        echo $this->dom->saveHTML();
    }
}
