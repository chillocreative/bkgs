<?php

namespace App\Support;

class TemplateRenderer
{
    /**
     * Replace `{{ name }}` placeholders (with optional whitespace) with values from $vars.
     * Unknown placeholders remain untouched. HTML is NOT escaped — for plain WhatsApp text only.
     */
    public static function render(string $template, array $vars): string
    {
        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            function ($m) use ($vars) {
                $key = $m[1];
                return array_key_exists($key, $vars) ? (string) $vars[$key] : $m[0];
            },
            $template
        );
    }
}
