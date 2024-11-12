<?php
namespace Views;

class Utils
{
    public static function replaceTemplateContent(
        $dom,
        $templateId,
        $newContent
    ): void {
        $template = $dom->getElementById($templateId);

        if ($template) {
            $newDom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $newDom->loadHTML(
                "<div>" . $newContent . "</div>",
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            libxml_clear_errors();

            $newContentFragment = $dom->createDocumentFragment();
            foreach ($newDom->documentElement->childNodes as $child) {
                $newContentFragment->appendChild(
                    $dom->importNode($child, true)
                );
            }

            $template->parentNode->replaceChild($newContentFragment, $template);
        }
    }
}
