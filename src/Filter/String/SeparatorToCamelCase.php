<?php

namespace Bone\Filter\String;

class SeparatorToCamelCase extends AbstractSeparator
{
    public function filter($value)
    {
        // garr! backslash any regex letters an' symbols
        $quote = preg_quote($this->separator, '#');

        // create some feckin' voodoo black magic regex
        $patterns = array(
            '#(' . $quote.')([A-Za-z]{1})#',
            '#(^[A-Za-z]{1})#',
        );

        $replace = array(
            function ($matches)
            {
                return ucwords($matches[2]);
            },
            function ($matches)
            {
                return $matches[1];
            },
        );

        $filtered = $value;
        foreach ($patterns as $index => $pattern)
        {
            $filtered = preg_replace_callback($pattern, $replace[$index], $filtered);
        }
        return $filtered;
    }
}