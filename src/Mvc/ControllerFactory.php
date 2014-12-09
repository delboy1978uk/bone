<?php
/**
 * User: delboy1978uk
 * Date: 09/12/2014
 * Time: 02:59
 */

namespace Bone\Mvc;

use Bone\ObjectFactory;
use Bone\Exception as Insult;


class ControllerFactory extends ObjectFactory
{
    /**
     * @param $controller_name
     * @param $request
     * @return \Bone\Mvc\Controller
     * @throws \Bone\Exception
     */
    public function create($controller_name, $request)
    {
        if(!class_exists($controller_name))
        {
            throw new Insult('Controller not found');
        }
        return new $controller_name($request);
    }
}