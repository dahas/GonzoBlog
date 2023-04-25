<?php

namespace Gonzo\Sources\traits;

trait Utils {

    public function germanDecimalNumberFormat(int|float $input): string
    {
        return number_format($input, 2, ',', '.');
    }

    public function sanitizeRequest(array $request): array
    {
        return array_map(function ($var) {
            $allowedTags = ["small", "dfn", "sup", "sub", "pre", "blockquote", "ins", "ul", "var", "samp", "del",
            "h6", "h5", "h4", "h3", "h2", "h1", "span", "br", "hr", "em", "address", "img", "kbd",
            "tt", "a", "acronym", "abbr", "code", "p", "i", "b", "strong", "dd", "dt", "dl", "ol",
            "li", "div", "big", "cite", "table", "tbody", "th", "tr", "td", "iframe"];
            return strip_tags($var, $allowedTags);
        }, $request);
    }
}