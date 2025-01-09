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

            if (empty($newContent))
                return;

            $newDom->loadHTML(
                mb_convert_encoding(
                    $newContent,
                    "HTML-ENTITIES",
                    "UTF-8"
                )
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
