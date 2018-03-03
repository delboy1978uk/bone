<?php

namespace Bone\Filter;

interface FilterInterface
{
    /**
     * @param $value
     * @return mixed
     */
    public function filter($value);
}