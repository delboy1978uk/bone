<?php

namespace Bone\Filter\String;

/**
 * Class SeparatorToCamelCase
 * @package Bone\Filter\String
 */
class SeparatorToCamelCase extends AbstractSeparator
{
    /** @var array */
    private $patterns;

    /**
     * settin' the regex gubbins, Cap'n!
     */
    public function __construct($separator)
    {
        parent::__construct($separator);

        // garr! backslash any regex letters an' symbols
        $quote = preg_quote($this->separator, '#');

        // create some feckin' voodoo black magic regex
        $this->patterns = [
            '#(' . $quote.')([A-Za-z]{1})#',
            '#(^[A-Za-z]{1})#',
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function filter($value)
    {
        $replace = $this->getReplaceCallback();
        $filtered = $value;

        foreach ($this->patterns as $index => $pattern)
        {
            $filtered = preg_replace_callback($pattern, $replace[$index], $filtered);
        }

        return $filtered;
    }

    /**
     * @return array
     */
    private function getReplaceCallback()
    {
        return [
            function ($matches) {
                return ucwords($matches[2]);
            },
            function ($matches) {
                return $matches[1];
            },
        ];
    }
}