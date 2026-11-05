<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list HTML sanitizer for rich-text content (Quill / Trix editors).
 *
 * Keeps formatting tags, removes scripts, event handlers (onclick=...),
 * javascript:/data: links and unsafe inline CSS. Unknown tags are unwrapped
 * (their text is kept); dangerous ones are dropped with their content.
 *
 * Use in Blade with @safehtml($value) instead of {!! $value !!}.
 */
class HtmlSanitizer
{
    /** Tags that are kept, with the attributes each may carry (besides the global ones). */
    protected const ALLOWED_TAGS = [
        'p' => [], 'div' => [], 'span' => [], 'br' => [], 'hr' => [],
        'b' => [], 'strong' => [], 'i' => [], 'em' => [], 'u' => [], 's' => [], 'del' => [], 'strike' => [],
        'sub' => [], 'sup' => [], 'small' => [], 'mark' => [],
        'blockquote' => [], 'pre' => [], 'code' => [],
        'ul' => [], 'ol' => ['start'], 'li' => ['data-list'],
        'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'a' => ['href', 'target', 'rel', 'title'],
        'img' => ['src', 'alt', 'width', 'height', 'title'],
        'figure' => [], 'figcaption' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
    ];

    /** Attributes allowed on every kept tag. */
    protected const GLOBAL_ATTRIBUTES = ['class', 'style', 'dir'];

    /** Tags removed together with everything inside them. */
    protected const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'noscript',
        'template', 'svg', 'math', 'form', 'input', 'textarea', 'button', 'select', 'option',
        'link', 'meta', 'base', 'title', 'head',
    ];

    /** CSS properties allowed in style="" (editor formatting only). */
    protected const ALLOWED_CSS = [
        'color', 'background-color', 'text-align', 'font-weight', 'font-style',
        'text-decoration', 'padding-left', 'margin-left', 'font-size', 'line-height',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        $doc = new DOMDocument('1.0', 'UTF-8');
        // The XML encoding hint makes libxml read the input as UTF-8.
        $doc->loadHTML(
            '<?xml encoding="UTF-8"?><div id="__sanitize_root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('__sanitize_root');

        if (! $root) {
            return e(strip_tags($html));
        }

        static::cleanChildren($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }

        return $output;
    }

    protected static function cleanChildren(DOMNode $node): void
    {
        // Copy first: the live list changes while nodes are removed.
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                continue;
            }

            if (! $child instanceof DOMElement) {
                // Comments, processing instructions, etc.
                $node->removeChild($child);
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            static::cleanChildren($child);

            if (! array_key_exists($tag, self::ALLOWED_TAGS)) {
                // Unwrap: keep the (already cleaned) children, drop the tag itself.
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            static::cleanAttributes($child, $tag);
        }
    }

    protected static function cleanAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::ALLOWED_TAGS[$tag]);

        $names = [];
        foreach ($element->attributes as $attribute) {
            $names[] = $attribute->name;
        }

        foreach ($names as $name) {
            $lower = strtolower($name);
            $value = $element->getAttribute($name);

            if (! in_array($lower, $allowed, true)) {
                $element->removeAttribute($name);
                continue;
            }

            if ($lower === 'href' && ! static::isSafeUrl($value, false)) {
                $element->removeAttribute($name);
            } elseif ($lower === 'src' && ! static::isSafeUrl($value, true)) {
                $element->removeAttribute($name);
            } elseif ($lower === 'style') {
                $style = static::cleanStyle($value);
                $style === '' ? $element->removeAttribute($name) : $element->setAttribute($name, $style);
            } elseif ($lower === 'target' && $value !== '_blank') {
                $element->removeAttribute($name);
            }
        }

        // Links opening a new tab must not get access to window.opener.
        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && ! $element->hasAttribute('src')) {
            $element->parentNode?->removeChild($element);
        }
    }

    protected static function isSafeUrl(string $url, bool $isImage): bool
    {
        // Strip whitespace/control chars that browsers ignore ("java\tscript:").
        $normalized = strtolower(preg_replace('/[\x00-\x20]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        if ($normalized === '') {
            return false;
        }

        if ($isImage && preg_match('#^data:image/(png|jpe?g|gif|webp);base64,#', $normalized)) {
            return true;
        }

        if (preg_match('#^(https?:|mailto:|tel:)#', $normalized)) {
            return true;
        }

        // Relative links and anchors (no scheme at all).
        return ! preg_match('#^[a-z][a-z0-9+.\-]*:#', $normalized) && ! str_starts_with($normalized, '//');
    }

    protected static function cleanStyle(string $style): string
    {
        $kept = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (! in_array($property, self::ALLOWED_CSS, true)) {
                continue;
            }

            // Values may only be colours, lengths, keywords — no url(), expression(), escapes, etc.
            if (! preg_match('/^[#a-zA-Z0-9.,%()\s\-]+$/', $value) || preg_match('/(url|expression|javascript|behavior|@import)/i', $value)) {
                continue;
            }

            $kept[] = $property . ': ' . $value;
        }

        return implode('; ', $kept);
    }
}
