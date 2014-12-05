<?php

namespace Bone\Mvc;

use Bone\Mvc\Request;
use Bone\Mvc\Response;
use Bone\Filter;


/**
 * Class Dispatcher
 * @package Bone\Mvc
 */
class Dispatcher
{
    // Garrrr! An arrrray!
    private $config = array();

    private $request;

    /** @var Controller */
    private $controller;

    private $response;


    public function __construct(Request $request,Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // what controller be we talkin' about?
        $filtered = Filter::filterString($this->request->getController(),'DashToCamelCase');
        $this->config['controller_name'] = '\App\Controller\\'.ucwords($filtered).'Controller';

        // whit be yer action ?
        $filtered = Filter::filterString($this->request->getAction(),'DashToCamelCase');
        $this->config['action_name'] = $filtered.'Action';
        $this->config['controller'] = $this->request->getController();
        $this->config['action'] = $this->request->getAction();
    }

    public function validateDestination()
    {
        // can we find th' darned controller?
        if(!$this->checkControllerExists())
        {
            $this->setNotFound();
            return;
        }
        $this->controller = new $this->config['controller_name']($this->request);
        if(!$this->checkActionExists())
        {
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
        return method_exists($this->controller,$this->config['action_name']);
    }



    public function fireCannons()
    {
        try
        {
            // Check we can call the controller
            $this->validateDestination();

            // run th' controller action
            $action = $this->config['action_name'];
            $this->controller->init();
            $this->controller->$action();
            $this->controller->postDispatch();

            $this->response->setHeaders($this->controller->getHeaders());

            /** @var \stdClass $view_vars  */
            $view_vars = $this->controller->view;

            $response_body = $this->controller->getBody();

            if($this->controller->hasViewEnabled())
            {
                $view = $this->config['controller'].'/'.$this->config['action'].'.twig';
                $response_body = $this->controller->getTwig()->render($view, (array) $view_vars);
            }

            if($this->controller->hasLayoutEnabled())
            {
                $response_body = $this->templateCheck($this->controller,$response_body);
            }
        }
        catch(Exception $e)
        {
            $this->request->setParam('error',$e);
            $dispatch = new \App\Controller\ErrorController($this->request);
            $dispatch->errorAction();
            /** @var \stdClass $view_vars  */
            $view_vars = (array) $dispatch->view;
            $view_vars = array_merge($view_vars,array('error' => $e));
            $view = 'error/error.twig';
            $response_body = $dispatch->getTwig()->render($view, $view_vars);
            $response_body = $this->templateCheck($dispatch,$response_body);
            
        }

        $this->response->setBody($response_body);

        $this->response->send();
    }

    /**
     *  @param Controller $controller
     *  @param string $content
     *  @return string
     */
    private function templateCheck($controller,$content)
    {
        $response_body = '';
        //check we be usin' th' templates in th' config
        $templates = Registry::ahoy()->get('templates');
        $template = ($templates != null) ? $templates[0] : null;
        if($template)
        {
            $response_body = $controller->getTwig()->render('layouts/'.$template.'.twig',array('content' => $content));
        }
        return $response_body;
    }

    /**
     * Sets controller to error and action to not found 
     * @return null
     */
    private function setNotFound()
    {
        $this->config['controller_name'] = '\App\Controller\ErrorController';
        $this->config['action_name'] = 'notFoundAction';
        $this->config['controller'] = 'error';
        $this->config['action'] = 'not-found';
        $this->controller = new $this->config['controller_name']($this->request);
    }



}