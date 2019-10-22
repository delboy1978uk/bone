<?php

namespace Bone\Mvc\Controller;

use Barnacle\Container;
use Bone\I18n\I18nAwareInterface;
use Bone\Mvc\Controller;
use Bone\Mvc\View\PlatesEngine;
use Bone\Server\SiteConfig;
use Bone\Server\SiteConfigAwareInterface;
use Bone\View\ViewAwareInterface;
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
        if ($controller instanceof I18nAwareInterface) {
            $controller->setTranslator($container->get(Translator::class));
        }

        if ($controller instanceof ViewAwareInterface) {
            $controller->setView($container->get(PlatesEngine::class));
        }

        if ($controller instanceof SiteConfigAwareInterface) {
            $controller->setSiteConfig($container->get(SiteConfig::class));
        }

        return $controller;
    }
}