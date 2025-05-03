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

        //manipolare headerDOM per togliere il link alla home dall'header quando siamo nella home (link circolare)
        $headerDOM= new \DOMDocument();
        libxml_use_internal_errors(true);
        $headerDOM->loadHTML('<?xml encoding="UTF-8">' . $headerContent);
        libxml_clear_errors();

        $xpathHeader = new DOMXpath($headerDOM);
        $headerLink = $xpathHeader->query('//a[@href="index.php"]')->item(0);
        if($headerLink && $this->currentPage === 'index'){            
            /** @var DOMElement $headerLink */
            $headerLink->removeAttribute('href'); // Rimuove il link
        }
        $headerContent = $headerDOM->saveHTML();

        //manipolare footerDOM per togliere il link alla pagina attuale dal nav (link circolare)
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

                            // Rimuove l'attributo href per disabilitare il link circolare
                            $link->removeAttribute('href');
                            // Aggiunge aria-current per accessibilità
                            $link->setAttribute('aria-current', 'page');
                            // Aggiunge tabindex -1 per rimuoverlo dall'ordine di tabulazione
                            $link->setAttribute('tabindex', '-1');
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

        // Pagine che richiedono login ma sono accessibili anche senza login
        $publicPages = ['settings.php', 'index.php'];
        $currentPage = basename($_SERVER['PHP_SELF']);

        // Controlla se è la pagina di login o register per non mostrare i rispettivi pulsanti
        $isLoginPage = ($currentPage === 'login.php');
        $isRegisterPage = ($currentPage === 'register.php');

        if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
            // Utente loggato
            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                '<a href="logout.php" class="nav-button danger" lang="en" id="logout">Logout</a>'
            );
        } else {
            // Utente non loggato

            // Per pagine protette, aggiungiamo un parametro di redirect
            $loginRedirect = '';
            if (!in_array($currentPage, $publicPages) && $currentPage !== 'login.php' && $currentPage !== 'register.php') {
                $loginRedirect = "?redirect={$currentPage}";
            }

            // Costruisce i pulsanti in base alla pagina corrente
            $buttonsHtml = '';

            // Mostra il pulsante login solo se non siamo nella pagina di login
            if (!$isLoginPage) {
                $buttonsHtml .= '<a href="login.php' . $loginRedirect . '" class="nav-button primary" lang="en">Login</a>';
            }

            // Mostra il pulsante register solo se non siamo nella pagina di register
            if (!$isRegisterPage) {
                $buttonsHtml .= '<a href="register.php" class="nav-button secondary">Registrati</a>';
            }

            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                $buttonsHtml
            );
        }
    }

    public function renderError(string $error, int $errorCode = 500): void
    {
        $protectedPages = ['profile.php', 'review.php'];
        $currentPage = basename($_SERVER['PHP_SELF']);

        $isAccessError = in_array($currentPage, $protectedPages) &&
            $error === "Devi effettuare il login per accedere";

        if ($isAccessError) {
            header("Location: error.php?code=401&page=" . urlencode($currentPage));
            exit();
        }

        if ($errorCode === 404) {
            $this->template = file_get_contents(__DIR__ . "/../templates/error.html");
            $this->dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $this->dom->loadHTML($this->template);
            libxml_clear_errors();

            Utils::replaceTemplateContent(
                $this->dom,
                "error-title-template",
                "<h1>Pagina non trovata</h1>"
            );

            Utils::replaceTemplateContent(
                $this->dom,
                "error-message-template",
                "<h3>La pagina che stai cercando non esiste</h3>"
            );

            Utils::replaceTemplateContent(
                $this->dom,
                "access-error-content",
                ""
            );

            Utils::replaceTemplateContent(
                $this->dom,
                "generic-error-content",
                ""
            );

            echo $this->dom->saveHTML();
            return;
        }

        $this->template = file_get_contents(__DIR__ . "/../templates/error.html");
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        libxml_clear_errors();

        Utils::replaceTemplateContent(
            $this->dom,
            "error-message-template",
            "<h3>" . htmlspecialchars($error) . "</h3>"
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "access-error-content",
            ""
        );

        Utils::replaceTemplateContent(
            $this->dom,
            "not-found-error-content",
            ""
        );

        echo $this->dom->saveHTML();
    }

    public function getDom(): \DOMDocument
    {
        return $this->dom;
    }
}