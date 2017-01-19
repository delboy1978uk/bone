<?php

namespace Bone;

use Bone\Filter\Exception as FilterException;

abstract class Filter
{
    /**
     * @param string $string
     * @param string $filter_type
     * @return string
     * @throws Filter\Exception
     */
    public static function filterString($string, $filter_type)
    {
        if(class_exists('Bone\Filter\String\\'.$filter_type))
        {
            $filter_name = 'Bone\Filter\String\\'.$filter_type;
            $filter = new $filter_name();
            return $filter->filter($string);
        }
        throw new FilterException(FilterException::NO_FILTER);
    }
}