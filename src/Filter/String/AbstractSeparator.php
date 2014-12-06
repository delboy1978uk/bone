<?php


namespace Bone\Filter\String;

abstract class AbstractSeparator
{
    protected  $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator)
    {
        $this->separator = $separator;
    }

    public abstract function filter($value);
}