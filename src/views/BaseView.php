<?php
namespace Views;

use Views\Utils;

abstract class BaseView
{
    protected $template;
    protected $dom;

    public function __construct($templatePath)
    {
        $this->template = file_get_contents($templatePath);
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        libxml_clear_errors();
    }

    public function render(array $data = [])
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
    }
}
