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
            $newDom = new \DOMDocument('1.0', 'UTF-8');
            $newDom->preserveWhiteSpace = false;
            $newDom->formatOutput = true;
            libxml_use_internal_errors(true);

            if (empty($newContent)) {
                return;
            }

            $newContent = '<?xml encoding="UTF-8">' .
                '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
                '<body>' . $newContent . '</body></html>';

            $newDom->loadHTML($newContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $newContentFragment = $dom->createDocumentFragment();
            $body = $newDom->getElementsByTagName('body')->item(0);

            if ($body) {
                foreach ($body->childNodes as $child) {
                    $newContentFragment->appendChild(
                        $dom->importNode($child, true)
                    );
                }
            }

            $template->parentNode->replaceChild($newContentFragment, $template);
        }
    }
}
