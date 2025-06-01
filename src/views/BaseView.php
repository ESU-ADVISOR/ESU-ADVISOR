<?php

namespace Views;

use Views\Utils;
use DOMDocument;
use DOMXPath;
use DOMElement;
use Models\PreferenzeUtenteModel;
use Models\UserModel;

abstract class BaseView
{
    protected $template;
    protected $dom;
    protected $currentPage;
    protected $breadcrumbData = null;

    /** @param string $templatePath */
    public function __construct($templatePath)
    {
        $this->template = file_get_contents($templatePath);
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        Utils::updatePreferencesFromSession($this->dom, $_SESSION);
        libxml_clear_errors();
        $this->currentPage = basename($_SERVER['PHP_SELF'], '.php');
    }

    protected function setBreadcrumb(array $data): void
    {
        $this->breadcrumbData = $data;
    }

    private function generateBreadcrumbHTML(): string
    {
        $html = '<h1>&rsaquo; ';
        
        if ($this->breadcrumbData && isset($this->breadcrumbData['parent'])) {
            $parent = $this->breadcrumbData['parent'];
            $html .= '<a href="' . htmlspecialchars($parent['url']) . '">' 
                   . htmlspecialchars($parent['title']) . '</a>';
            $html .= ' &rsaquo; ';
            
            $currentTitle = $this->breadcrumbData['current'] ?? ucfirst($this->currentPage);
            $prefix = $this->breadcrumbData['prefix'] ?? '';
            $html .= htmlspecialchars($prefix . $currentTitle);
        } else {
            if ($this->currentPage === 'index') {
                $html .= 'Home';
            } else {
                $html .= '<a href="index.php">Home</a>';
                $html .= ' &rsaquo; ';
                
                $pageNames = [
                    'profile' => 'Profilo',
                    'review' => 'Review',
                    'settings' => 'Impostazioni', 
                    'login' => 'Login',
                    'register' => 'Register'
                ];
                
                $currentTitle = $this->breadcrumbData['current'] ?? 
                               ($pageNames[$this->currentPage] ?? ucfirst($this->currentPage));
                
                $html .= htmlspecialchars($currentTitle);
            }
        }

        $html .= '</h1>';
        return $html;
    }

    public function render(array $data = []): void
    {
        $headerContent = file_get_contents(
            __DIR__ . "/../templates/header.html"
        );
        $footerContent = file_get_contents(
            __DIR__ . "/../templates/footer.html"
        );
        $sidebarContent = file_get_contents(
            __DIR__ . "/../templates/sidebar.html"
        );

        $headerDOM = new \DOMDocument();
        libxml_use_internal_errors(true);
        $headerDOM->loadHTML($headerContent);
        Utils::updatePreferencesFromSession($this->dom, $_SESSION);
        libxml_clear_errors();

        $xpathHeader = new DOMXpath($headerDOM);
        $headerLink = $xpathHeader->query('//a[@href="index.php"]')->item(0);
        if ($headerLink && $this->currentPage === 'index') {
            /** @var DOMElement $headerLink */
            $span = $headerDOM->createElement('span');
            foreach ($headerLink->attributes as $attr) {
                if ($attr->name !== 'href') {
                    $span->setAttribute($attr->name, $attr->value);
                }
            }
            while ($headerLink->firstChild) {
                $span->appendChild($headerLink->firstChild);
            }
            $headerLink->parentNode->replaceChild($span, $headerLink);
        }
        $headerContent = $headerDOM->saveHTML();

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
                        }
                        $span = $footerDOM->createElement('span');
                        foreach ($link->attributes as $attr) {
                            if ($attr->name !== 'href') {
                                $span->setAttribute($attr->name, $attr->value);
                            }
                        }
                        $span->setAttribute('aria-current', 'page');
                        $span->setAttribute('tabindex', '-1');
                        $span->setAttribute('rel', 'nofollow');
                        while ($link->firstChild) {
                            $span->appendChild($link->firstChild);
                        }
                        $link->parentNode->replaceChild($span, $link);
                    }
                }
            }
        }

        $sidebarDOM = new \DOMDocument();
        libxml_use_internal_errors(true);
        $sidebarDOM->loadHTML($sidebarContent);
        libxml_clear_errors();

        $sidebarXpath = new DOMXPath($sidebarDOM);
        $sidebarLinks = $sidebarXpath->query('//a[contains(@class, "sidebar-nav-link")]');
        if ($sidebarLinks) {
            foreach ($sidebarLinks as $link) {
                /** @var DOMElement $link */
                if ($link instanceof DOMElement && $link->hasAttribute('href')) {
                    $href = $link->getAttribute('href');
                    $pageName = basename($href, '.php');
                    if ($pageName === $this->currentPage) {
                        $parentNode = $link->parentNode;
                        if ($parentNode instanceof DOMElement && $parentNode->nodeName === 'li') {
                            $currentClass = $parentNode->getAttribute('class') ?? '';
                            $parentNode->setAttribute('class', trim($currentClass . ' active'));
                        }
                        $span = $sidebarDOM->createElement('span');
                        $span->setAttribute('class', $link->getAttribute('class'));
                        $span->setAttribute('aria-current', 'page');
                        $span->setAttribute('tabindex', '-1');
                        $span->setAttribute('rel', 'nofollow');
                        while ($link->firstChild) {
                            $span->appendChild($link->firstChild);
                        }
                        $link->parentNode->replaceChild($span, $link);
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

        $sidebarIconPaths = [
            'sidebar-home-icon-template' => __DIR__ . '/../../public_html/images/home.svg',
            'sidebar-review-icon-template' => __DIR__ . '/../../public_html/images/review.svg',
            'sidebar-profile-icon-template' => __DIR__ . '/../../public_html/images/profile.svg',
            'sidebar-settings-icon-template' => __DIR__ . '/../../public_html/images/settings.svg'
        ];

        foreach ($sidebarIconPaths as $templateId => $svgPath) {
            if (file_exists($svgPath)) {
                $svgContent = file_get_contents($svgPath);
                Utils::replaceTemplateContent(
                    $sidebarDOM,
                    $templateId,
                    $svgContent
                );
            }
        }

        $footerContent = $footerDOM->saveHTML();
        $sidebarContent = $sidebarDOM->saveHTML();

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
        Utils::replaceTemplateContent(
            $this->dom,
            "sidebar-template",
            $sidebarContent
        );

        $publicPages = ['settings.php', 'index.php'];
        $currentPage = basename($_SERVER['PHP_SELF']);

        $isLoginPage = ($currentPage === 'login.php');
        $isRegisterPage = ($currentPage === 'register.php');

        $isUserLoggedIn = isset($_SESSION["username"]) && !empty($_SESSION["username"]) && $_SESSION["username"] !== '';
        
        if ($isUserLoggedIn) {
            $logoutButton = '<a href="logout.php" class="nav-button danger" lang="en" id="logout">Logout</a>';
            $sidebarLogoutButton = '<a href="logout.php" class="sidebar-auth-button danger" lang="en" id="sidebar-logout">Logout</a>';
            
            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                $logoutButton
            );
            Utils::replaceTemplateContent(
                $this->dom,
                "sidebar-session-buttons-template",
                $sidebarLogoutButton
            );
        } else {
            $loginRedirect = '';
            if (!in_array($currentPage, $publicPages) && $currentPage !== 'login.php' && $currentPage !== 'register.php') {
                $loginRedirect = "?redirect={$currentPage}";
            }

            $buttonsHtml = '';
            if (!$isLoginPage) {
                $buttonsHtml .= '<a href="login.php' . $loginRedirect . '" class="nav-button primary" lang="en">Login</a>';
            }
            if (!$isRegisterPage) {
                $buttonsHtml .= '<a href="register.php" class="nav-button secondary">Registrati</a>';
            }

            $sidebarButtonsHtml = '';
            if (!$isLoginPage) {
                $sidebarButtonsHtml .= '<a href="login.php' . $loginRedirect . '" class="sidebar-auth-button primary" lang="en">Login</a>';
            }
            if (!$isRegisterPage) {
                $sidebarButtonsHtml .= '<a href="register.php" class="sidebar-auth-button secondary">Registrati</a>';
            }

            Utils::replaceTemplateContent(
                $this->dom,
                "session-buttons-template",
                $buttonsHtml
            );
            Utils::replaceTemplateContent(
                $this->dom,
                "sidebar-session-buttons-template",
                $sidebarButtonsHtml
            );
        }

        $breadcrumbHTML = $this->generateBreadcrumbHTML();
        Utils::replaceTemplateContent(
            $this->dom,
            "breadcrumb-template",
            $breadcrumbHTML
        );
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