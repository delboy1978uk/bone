<?php

namespace Bone\Mvc;

use Bone\Filter;
use Exception;
use ReflectionClass;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\Stream;

/**
 * Class Dispatcher
 * @package Bone\Mvc
 */
class Dispatcher
{
    // Garrrr! An arrrray!
    private $config = array();

    /** @var RequestInterface $request */
    private $request;

    /** @var Controller */
    private $controller;

    /** @var ResponseInterface $response */
    private $response;


    public function __construct(RequestInterface $request, ResponseInterface $response)
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
    }


    /**
     *  Gaaarrr! Check the Navigator be readin' the map!
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

        // where's the bloody action?
        if (!$this->checkActionExists()) {
            $this->setNotFound();
        }
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
    private function getResponseBody()
    {
        /** @var \stdClass $view_vars */
        $view_vars = $this->controller->view;

        $response_body = $this->controller->getBody();

        if ($this->controller->hasViewEnabled()) {
            $view = $this->config['controller'] . '/' . $this->config['action'] . '.twig';
            try {
                $response_body = $this->controller->getTwig()->render($view, (array)$view_vars);
            } catch (Exception $e) {
                throw $e;
            }
        }

        if ($this->controller->hasLayoutEnabled()) {
            $response_body = $this->templateCheck($this->controller, $response_body);
        }
        return $response_body;
    }


    /**
     *
     */
    public function fireCannons()
    {
        try {
            // Where be the navigator? Be we on course?
            $this->checkNavigator();

            // boom! direct hit Cap'n! Be gettin' the booty!
            $this->plunderEnemyShip();

            // show th' cap'n th' booty
            $booty = $this->getResponseBody();
        } catch (Exception $e) {
            $booty = $this->sinkingShip($e);
        }

        $this->response->getBody()->write($booty);

        // report back to th' cap'n
        $this->setHeaders();

        $emitter = new SapiEmitter();
        return $emitter->emit($this->response);
    }

    private function setHeaders()
    {
        foreach ($this->controller->getHeaders() as $key => $value) {
            $this->response = $this->response->withHeader($key, $value);
        }
    }


    private function plunderEnemyShip()
    {
        // run th' controller action
        $action = $this->config['action_name'];
        $this->controller->init();
        $this->controller->$action();
        $this->controller->postDispatch();
    }


    public function sinkingShip($e)
    {
//        $this->request->setParam('error', $e);
        $this->controller = class_exists('\App\Controller\ErrorController') ? new \App\Controller\ErrorController($this->request) : new Controller($this->request);

        $reflection = new ReflectionClass(get_class($this->controller));
        $method = $reflection->getMethod('errorAction');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);
        $this->controller->error = $e;
        $this->config['controller'] = 'error';
        $this->config['action'] = 'error';
        return $this->getResponseBody();
    }


    /**
     * @param Controller $controller
     * @param string $content
     * @return string
     */
<<<<<<< HEAD
    private function templateCheck($controller, $content, array $view_vars = [])
=======
    private function templateCheck($controller, $content)
>>>>>>> psr7
    {
        $response_body = '';
        //check we be usin' th' templates in th' config
        $templates = Registry::ahoy()->get('templates');
        $template = ($templates != null) ? $templates[0] : null;
<<<<<<< HEAD
        if($template)
        {
            $view_vars['content'] = $content;
            $response_body = $controller->getTwig()->render('layouts/'.$template.'.twig',$view_vars);
=======
        if ($template) {
            $response_body = $controller->getTwig()->render('layouts/' . $template . '.twig', array('content' => $content));
>>>>>>> psr7
        }
        return $response_body;
    }


    /**
     * Sets controller to error and action to not found
<<<<<<< HEAD
     * @return null
=======
     * @return void
>>>>>>> psr7
     */
    private function setNotFound()
    {
        $this->config['controller_name'] = class_exists('\App\Controller\ErrorController') ? '\App\Controller\ErrorController' : '\Bone\Mvc\Controller';
        $this->config['action_name'] = 'notFoundAction';
        $this->config['controller'] = 'error';
        $this->config['action'] = 'not-found';
        $this->controller = new $this->config['controller_name']($this->request);
    }


<<<<<<< HEAD

}
=======
}
>>>>>>> psr7
