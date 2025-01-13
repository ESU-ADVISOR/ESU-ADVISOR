<?php

namespace Views;

use Views\Utils;
use DOMDocument;
use DOMXPath;
use DOMElement;

abstract class BaseView
{
    protected $template;
    protected $dom;
    protected $currentPage;

    /** @param string $templatePath */
    public function __construct($templatePath)
    {
        $this->template = file_get_contents($templatePath);
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        libxml_clear_errors();

        $this->currentPage = basename($_SERVER['PHP_SELF'], '.php');
    }

    public function render(array $data = []): void
    {
        $headerContent = file_get_contents(
            __DIR__ . "/../templates/header.html"
        );
        $footerContent = file_get_contents(
            __DIR__ . "/../templates/footer.html"
        );

        $footerDOM = new \DOMDocument();
        libxml_use_internal_errors(true);
        $footerDOM->loadHTML($footerContent);
        libxml_clear_errors();

        $xpath = new DOMXPath($footerDOM);
        $links = $xpath->query('//a');
        if ($links) {
            foreach ($links as $link) {
                /** @var DOMElement $link */
                if ($link instanceof DOMElement && $link->hasAttribute('href')) {
                    $href = $link->getAttribute('href');
                    $pageName = basename($href, '.php');
                    if ($pageName === $this->currentPage) {
                        $parentNode = $link->parentNode;
                        if ($parentNode instanceof DOMElement && $parentNode->nodeName === 'li') {
                            $currentClass = $parentNode->getAttribute('class') ?? '';
                            $parentNode->setAttribute('class', trim($currentClass . ' active'));
                            $link->removeAttribute('href');
                        }
                    }
                }
            }
        }

        $iconPaths = [
            'home-icon-template' => __DIR__ . '/../../public_html/images/home.svg',
            'review-icon-template' => __DIR__ . '/../../public_html/images/review.svg',
            'profile-icon-template' => __DIR__ . '/../../public_html/images/profile.svg',
            'settings-icon-template' => __DIR__ . '/../../public_html/images/settings.svg'
        ];

        foreach ($iconPaths as $templateId => $svgPath) {
            if (file_exists($svgPath)) {
                $svgContent = file_get_contents($svgPath);
                Utils::replaceTemplateContent(
                    $footerDOM,
                    $templateId,
                    $svgContent
                );
            }
        }
        $footerContent = $footerDOM->saveHTML();

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
                '<a href="logout.php" class="nav-button danger" lang="en">Logout</a>'
            );
        } else {
            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                '<a href="login.php" class="nav-button primary" lang="en">Login</a><a href="register.php" class="nav-button secondary">Registrati</a>'
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
