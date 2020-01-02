<?php

namespace Bone\Mvc\View\Extension\Plates;

use Bone\View\Helper\AlertBox as AlertBoxHelper;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class AlertBox implements ExtensionInterface
{
    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('alert', [$this, 'alertBox']);
    }

    /**
     * @param array $message
     * @return string
     */
    public function alertBox(array $message) : string
    {
        $box = new AlertBoxHelper();

        return $box->alertBox($message);
    }
}
