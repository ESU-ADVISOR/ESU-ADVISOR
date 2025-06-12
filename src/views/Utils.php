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

            // $newContent = '<?xml encoding="UTF-8">' .
            $newContent =    '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
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

    public static function updatePreferencesFromSession($dom, $session)
    {
        $html = $dom->getElementsByTagName('html')->item(0);
        if (!$html) {
            return;
        }

        // Apply theme
        $classes = [];

        if (isset($session['tema']) && !empty($session['tema'])) {
            if ($session['tema'] === 'scuro') {
                $classes[] = 'theme-dark';
            } elseif ($session['tema'] === 'chiaro') {
                $classes[] = 'theme-light';
            }
        }

        if (!empty($session['dimensione_testo'])) {
            $classes[] = 'text-size-' . $session['dimensione_testo'];
        }
        if (!empty($session['dimensione_icone'])) {
            $classes[] = 'icon-size-' . $session['dimensione_icone'];
        }
        if (!empty($session['modifica_font'])) {
            $classes[] = 'font-' . $session['modifica_font'];
        }

        if (!empty($classes)) {
            $html->setAttribute('class', implode(' ', $classes));
        }
    }

    public static function insertAfterOpeningTag($dom, $tagName, $content, $index = 0): void
    {
        $elements = $dom->getElementsByTagName($tagName);

        if ($elements->length > $index) {
            $element = $elements->item($index);

            $newDom = new \DOMDocument('1.0', 'UTF-8');
            $newDom->preserveWhiteSpace = false;
            $newDom->formatOutput = true;
            libxml_use_internal_errors(true);

            $html = '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
                '<body>' . $content . '</body></html>';

            $newDom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $fragment = $dom->createDocumentFragment();
            $body = $newDom->getElementsByTagName('body')->item(0);

            if ($body) {
                foreach ($body->childNodes as $child) {
                    $fragment->appendChild($dom->importNode($child, true));
                }

                if ($element->firstChild) {
                    $element->insertBefore($fragment, $element->firstChild);
                } else {
                    $element->appendChild($fragment);
                }
            }
        }
    }
}
