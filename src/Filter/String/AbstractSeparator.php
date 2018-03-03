<?php

namespace Bone\Filter\String;

use Bone\Filter\FilterInterface;

abstract class AbstractSeparator implements FilterInterface
{
    /**
     * @var string $separator
     */
    protected  $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @param string $value
     * @return string mixed
     */
    abstract public function filter($value);
}