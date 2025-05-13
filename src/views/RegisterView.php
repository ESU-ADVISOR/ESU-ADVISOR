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
        $breadcrumbContent = '<h1 lang="en" >Ti trovi in: Register</h1>';
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
            $successHtml = "<div class='success'>{$data["success"]}</div><p>Ora puoi effettuare il <a href='login.php'>login</a></p>";
            Utils::replaceTemplateContent(
                $this->dom,
                "server-response-template",
                $successHtml
            );
        }

        echo $this->dom->saveHTML();
    }
}
