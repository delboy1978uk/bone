<?php

namespace Bone\Mvc;

use Bone\Exception as Insult;
use Psr\Http\Message\RequestInterface;

class ControllerFactory
{
    /**
     * @param $controller_name
     * @param RequestInterface $request
     * @return \Bone\Mvc\Controller
     * @throws \Bone\Exception
     */
    public function create($controller_name, RequestInterface $request)
    {
        if(!class_exists($controller_name))
        {
            throw new Insult('Controller not found');
        }
        /** @var Controller $controller */
        $controller = new $controller_name($request);
        return $controller;
    }
}