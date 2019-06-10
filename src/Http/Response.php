<?php

namespace Bone\Http;

use Zend\Diactoros\Response as PsrResponse;

class Response extends PsrResponse
{
    private $module;

    private $controller;

    private $action;

    private $renderView;

    private $renderTemplate;
}