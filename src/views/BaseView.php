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
    protected $breadcrumbData = null;

    protected $customTitle = null;
    protected $customDescription = null;
    protected $customKeywords = null;

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


    public function setTitle(string $title): void
    {
        $this->customTitle = $title;
    }

    public function setDescription(string $description): void
    {
        $this->customDescription = $description;
    }


    public function setKeywords(string $keywords): void
    {
        $this->customKeywords = $keywords;
    }

    protected function addBasicSEO(): void
    {
        $head = $this->dom->getElementsByTagName('head')->item(0);
        if (!$head) {
            return;
        }

        $title = $this->customTitle ?? $this->getDefaultTitle();
        $description = $this->customDescription ?? $this->getDefaultDescription();
        $keywords = $this->customKeywords ?? $this->getDefaultKeywords();

        $titleElements = $this->dom->getElementsByTagName('title');
        if ($titleElements->length > 0) {
            $titleElements->item(0)->textContent = $title;
        }

        $this->updateOrCreateMeta('name', 'description', $description);
        $this->updateOrCreateMeta('name', 'keywords', $keywords);
        $this->updateOrCreateMeta('name', 'author', 'Giacomo Loat, Giulio Bottacin, Malik Giafar Mohamed, Manuel Felipe Vasquez - Università di Padova');
    }


    private function getDefaultTitle(): string
    {
        $baseTitle = "ESU Advisor";

        switch ($this->currentPage) {
            case 'index':
                return "Menu Mense Universitarie Padova | $baseTitle";
            case 'piatto':
                return "Dettagli Piatto | $baseTitle";
            case 'login':
                return "Accedi | $baseTitle";
            case 'register':
                return "Registrati | $baseTitle";
            case 'profile':
                return "Il Mio Profilo | $baseTitle";
            case 'review':
                return "Scrivi Recensione | $baseTitle";
            case 'settings':
                return "Impostazioni | $baseTitle";
            default:
                return $baseTitle;
        }
    }

    private function getDefaultDescription(): string
    {
        switch ($this->currentPage) {
            case 'index':
                return "Scopri i menu giornalieri delle mense ESU di Padova. Trova orari, località e recensioni dei piatti delle mense universitarie.";
            case 'piatto':
                return "Leggi recensioni e dettagli sui piatti delle mense universitarie di Padova. Scopri ingredienti, allergeni e valutazioni degli studenti.";
            case 'login':
                return "Accedi al tuo account ESU Advisor per lasciare recensioni sui piatti delle mense universitarie di Padova.";
            case 'register':
                return "Crea il tuo account ESU Advisor per recensire i piatti delle mense universitarie e personalizzare le tue preferenze alimentari.";
            case 'profile':
                return "Gestisci il tuo profilo ESU Advisor: visualizza le tue recensioni e le statistiche delle tue valutazioni.";
            case 'review':
                return "Condividi la tua esperienza: scrivi una recensione sui piatti delle mense universitarie di Padova per aiutare altri studenti.";
            case 'settings':
                return "Personalizza la tua esperienza ESU Advisor: gestisci preferenze alimentari, accessibilità e impostazioni account.";
            default:
                return "Consulta i menu delle mense universitarie di Padova, leggi recensioni e condividi la tua esperienza sui piatti dell'ESU.";
        }
    }


    private function getDefaultKeywords(): string
    {
        switch ($this->currentPage) {
            case 'index':
                return "mense,università,Padova,menu,ESU,cibo,studenti,recensioni,piatti,orari";
            case 'piatto':
                return "piatti,mense,Padova,recensioni,valutazioni,ingredienti,università,allergeni,ESU";
            case 'login':
                return "login,mense,piatti,Padova,account,ESU,account,studente";
            case 'register':
                return "registrazione,mensa,Padova,nuovo account,ESU,studente,università,Padova";
            case 'profile':
                return "profilo,studente,recensioni,mensa,statistiche,valutazioni";
            case 'review':
                return "recensione,piatto,mensa,valutazione,università,esperienza,Padova";
            case 'settings':
                return "impostazioni,mensa,preferenze alimentari,allergeni,accessibilità";
            default:
                return "mense,Padova,ESU,recensioni,piatti,menu,università";
        }
    }


    private function updateOrCreateMeta(string $attribute, string $name, string $content): void
    {
        $head = $this->dom->getElementsByTagName('head')->item(0);
        $xpath = new DOMXPath($this->dom);
        $existing = $xpath->query("//meta[@{$attribute}='{$name}']")->item(0);

        if ($existing) {
            if ($existing instanceof DOMElement) {
                $existing->setAttribute('content', $content);
            }
        } else {
            $meta = $this->dom->createElement('meta');
            $meta->setAttribute($attribute, $name);
            $meta->setAttribute('content', $content);
            $head->appendChild($meta);
        }
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
            $pageNames = [
                'index' => 'Home',
                'profile' => 'Profilo',
                'review' => 'Recensione',
                'settings' => 'Impostazioni',
                'login' => 'Login',
                'register' => 'Registrati',
                'error' => 'Errore'
            ];

            $currentTitle = $this->breadcrumbData['current'] ??
                ($pageNames[$this->currentPage] ?? ucfirst($this->currentPage));

            $html .= htmlspecialchars($currentTitle);
        }

        $html .= '</h1>';
        return $html;
    }

    public function render(array $data = []): void
    {
        $this->addBasicSEO();

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
            if (
                in_array($currentPage, $publicPages) && $currentPage !== 'login.php' && $currentPage !== 'register.php' && $currentPage !== 'error.php'
            ) {
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

    public function renderError(string  $errorTitle, string $errorMessage, int $errorCode = 500): void
    {
        $currentPage = basename($_SERVER['PHP_SELF']);
        header("Location: error.php?code=" . $errorCode . "&page=" . urlencode($currentPage) . "&title=" . $errorTitle . "&message=" . $errorMessage);
        exit();
    }

    public function getDom(): \DOMDocument
    {
        return $this->dom;
    }
}
