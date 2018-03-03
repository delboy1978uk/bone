<?php

namespace Bone;

use Bone\Filter\Exception as FilterException;
use Bone\Filter\FilterInterface;

abstract class Filter
{
    /**
     * @param string $string
     * @param string $filter_type
     * @return string
     * @throws Filter\Exception
     */
    public static function filterString($string, $filterType)
    {
        $boneFilter = 'Bone\Filter\String\\'.$filterType;

        if (class_exists($filterType)) {
            $filter = new $filterType();
        } else if (class_exists($boneFilter)) {
            $filter = new $boneFilter();
        }

        if ($filter instanceof FilterInterface) {
            return $filter->filter($string);
        }

        throw new FilterException(FilterException::NO_FILTER);
    }
}