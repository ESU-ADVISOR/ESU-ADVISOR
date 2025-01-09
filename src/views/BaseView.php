<?php

namespace Views;

use Views\Utils;

abstract class BaseView
{
    protected $template;
    protected $dom;

    /** @param string $templatePath */
    public function __construct($templatePath)
    {
        $this->template = file_get_contents($templatePath);
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        libxml_clear_errors();
    }

    public function render(array $data = []): void
    {
        $headerContent = file_get_contents(
            __DIR__ . "/../templates/header.html"
        );
        $footerContent = file_get_contents(
            __DIR__ . "/../templates/footer.html"
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "header-template",
            $headerContent
        );
        Utils::replaceTemplateContent(
            $this->dom,
            "footer-template",
            $footerContent
        );
        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                '<a href="logout.php" class="nav-button danger">Logout</a>'
            );
        } else {
            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                '<a href="login.php" class="nav-button primary">Login</a><a href="register.php" class="nav-button secondary">Register</a>'
            );
        }
    }
    public function renderError(string $error): void
    {
        $this->template = file_get_contents(
            __DIR__ . "/../templates/error.html"
        );
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        libxml_clear_errors();

        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h3>" . htmlspecialchars($error) . "</h3>"
        );

        echo $this->dom->saveHTML();
    }
}
