<?php

namespace Bone\Mvc\Controller;

use Barnacle\Container;
use Bone\Mvc\Controller;
use Zend\I18n\Translator\Translator;

class Init
{
    /**
     * @param Controller $controller
     * @param Container $container
     * @return Controller
     */
    public static function controller(Controller $controller, Container $container): Controller
    {
        if ($controller instanceof LocaleAwareInterface) {
            $controller->setTranslator($container->get(Translator::class));
        }

        return $controller;
    }
}