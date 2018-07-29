<?php

namespace Bone\Mvc;

use Bone\Filter;
use Exception;
use ReflectionClass;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * Class Dispatcher
 * @package Bone\Mvc
 */
class Dispatcher
{
    // Garrrr! An arrrray!
    private $config = array();

    /** @var ServerRequestInterface $request */
    private $request;

    /** @var Controller */
    private $controller;

    /** @var ResponseInterface $response */
    private $response;

    /**
     * Dispatcher constructor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @throws Filter\Exception
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        $router = new Router($request);
        $router->parseRoute();

        // what controller be we talkin' about?
        $filtered = Filter::filterString($router->getController(), 'DashToCamelCase');
        $this->config['controller_name'] = '\App\Controller\\' . ucwords($filtered) . 'Controller';

        // whit be yer action ?
        $filtered = Filter::filterString($router->getAction(), 'DashToCamelCase');
        $this->config['action_name'] = $filtered . 'Action';
        $this->config['controller'] = $router->getController();
        $this->config['action'] = $router->getAction();
        $this->config['params'] = $router->getParams();
    }


    /**
     *  Gaaarrr! Check the Navigator be readin' the map!
     * @return null|void
     */
    public function checkNavigator()
    {
        // can we find th' darned controller?
        if (!$this->checkControllerExists()) {
            $this->setNotFound();
            return;
        }

        // gaaarr! there be the controller!
        $this->controller = new $this->config['controller_name']($this->request);
        $this->controller->params = isset($this->config['params']) ? $this->config['params'] : null;

        // where's the bloody action?
        if (!$this->checkActionExists()) {
            $this->setNotFound();
        }
        return null;
    }


    /**
     * @return bool
     */
    private function checkControllerExists()
    {
        return class_exists($this->config['controller_name']);
    }


    /**
     * @return bool
     */
    private function checkActionExists()
    {
        return method_exists($this->controller, $this->config['action_name']);
    }


    /**
     * @return string
     * @throws \Exception
     */
    private function distributeBooty()
    {
        /** @var \stdClass $viewVars */
        $viewVars = $this->controller->view;

        if ($viewVars instanceof ResponseInterface) {
            $this->response = $viewVars;
            return $this->sendResponse();
        }

        $responseBody = $this->controller->getBody();

        if ($this->controller->hasViewEnabled()) {
            $view = $this->config['controller'] . '/' . $this->config['action'];
            try {
                $responseBody = $this->controller->getViewEngine()->render($view, (array) $viewVars);
            } catch (Exception $e) {
                throw $e;
            }
        }

        if ($this->controller->hasLayoutEnabled()) {
            $responseBody = $this->templateCheck($this->controller, $responseBody);
        }
        $this->prepareResponse($responseBody);
        $this->sendResponse();
    }


    /**
     * @throws Exception
     */
    public function fireCannons()
    {
        try {
            // Garr! Check the route with th' navigator
            $this->checkNavigator();

            // Fire cannons t' th' controller action
            $this->plunderEnemyShip();

            // Share the loot! send out th' response
            $this->distributeBooty();
        } catch (Exception $e) {
            $this->sinkingShip($e);
        }
    }

    /**
     * @param $booty
     */
    private function prepareResponse($booty)
    {
        $this->response->getBody()->write($booty);
        $this->setHeaders();
        $this->setStatusCode();
    }


    private function sendResponse()
    {
        $emitter = new SapiEmitter();
        $emitter->emit($this->response);
    }

    private function setHeaders()
    {
        foreach ($this->controller->getHeaders() as $key => $value) {
            $this->response = $this->response->withHeader($key, $value);
        }
    }

    private function setStatusCode()
    {
        $status = $this->controller->getStatusCode();
        if ($status != 200) {
            try {
                $this->response = $this->response->withStatus($status);
            } catch (Exception $e) {
                $this->response = $this->response->withStatus(500);
            }

        }
    }


    private function plunderEnemyShip()
    {
        // run th' controller action
        $action = $this->config['action_name'];

        $this->controller->init();
        $vars = $this->controller->$action();

        if (is_array($vars)) {

            $viewVars = (array) $this->controller->view;
            $view = (object) array_merge($vars, $viewVars);
            $this->controller->view = $view;

        } elseif ($vars instanceof ResponseInterface) {

            $this->controller->view = $vars;

        }

        $this->controller->postDispatch();
    }

    /**
     * @param Exception $e
     * @return string
     * @throws Exception
     */
    public function sinkingShip(Exception $e)
    {
        $controllerName = class_exists('\App\Controller\ErrorController') ? 'App\Controller\ErrorController' : 'Bone\Mvc\Controller';
        $this->controller = new $controllerName($this->request);
        $this->controller->setParam('error', $e);
        $reflection = new ReflectionClass(get_class($this->controller));
        $method = $reflection->getMethod('errorAction');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);
        $this->controller->error = $e;
        $this->config['controller'] = 'error';
        $this->config['action'] = 'error';
        $this->response = $this->response->withStatus(500);
        $this->distributeBooty();
    }


    /**
     * @param Controller $controller
     * @param string $content
     * @return string
     */
    private function templateCheck($controller, $content)
    {
        $responseBody = '';
        //check we be usin' th' templates in th' config
        $templates = Registry::ahoy()->get('templates');
        $template = $this->getTemplateName($templates);
        if ($template !== null) {
            $responseBody = $controller->getViewEngine()->render('layouts/' . $template, array('content' => $content));
        }
        return $responseBody;
    }

    /**
     * @param mixed $templates
     * @return string|null
     */
    private function getTemplateName($templates)
    {
        if (is_null($templates)) {
            return null;
        } elseif (is_array($templates)) {
            return (string) $templates[0];
        }
        return (string) $templates;
    }

    /**
     * Sets controller to error and action to not found
     * @return void
     */
    private function setNotFound()
    {
        $this->config['controller_name'] = class_exists('\App\Controller\ErrorController') ? '\App\Controller\ErrorController' : '\Bone\Mvc\Controller';
        $this->config['action_name'] = 'notFoundAction';
        $this->config['controller'] = 'error';
        $this->config['action'] = 'not-found';
        $this->controller = new $this->config['controller_name']($this->request);
        $this->response = $this->response->withStatus(404);
    }
}
