<?php

namespace Bone\View\Helper\Exception;

use Exception;

class PaginatorException extends Exception
{
    public const NO_PAGE_COUNT = 'No total page count';
    public const NO_URL = 'No URL set';
    public const NO_URL_PART = 'No URL part set';
    public const NO_CURRENT_PAGE = 'No current page count';
}
