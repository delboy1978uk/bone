<?php

namespace Bone\Filter;

use Bone\Exception as BoneException;

class Exception extends BoneException
{
    const NO_FILTER = 'A filter o\' that name does not exist!';
}