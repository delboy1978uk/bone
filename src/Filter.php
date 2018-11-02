<?php

namespace Bone;

use Bone\Filter\FilterException;
use Bone\Filter\FilterInterface;

abstract class Filter
{
    /**
     * @param string $string
     * @param string $filterType
     * @return string
     * @throws FilterException
     */
    public static function filterString($string, $filterType)
    {
        $filter = null;
        $boneFilter = 'Bone\Filter\String\\' . $filterType;

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