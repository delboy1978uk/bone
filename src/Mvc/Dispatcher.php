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


    /**
     *  Gaaarrr! Check the Navigator be readin' the map!
     */
    public function checkNavigator()
    {
        // can we find th' darned controller?
        if(!$this->checkControllerExists())
        {
            $this->setNotFound();
            return;
        }

        // gaaarr! there be the controller!
        $this->controller = new $this->config['controller_name']($this->request);

        // where's the bloody action?
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


    /**
     * @return string
     */
    private function getResponseBody()
    {
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
        return $response_body;
    }




    /**
     *
     */
    public function fireCannons()
    {
        try
        {
            // Where be the navigator? Be we on course?
            $this->checkNavigator();

            // boom! direct hit Cap'n! Be gettin' the booty!
            $this->plunderEnemyShip();

            // report back to th' cap'n
            $this->response->setHeaders($this->controller->getHeaders());

            // show th' cap'n th' booty
            $booty = $this->getResponseBody();
        }
        catch(Exception $e)
        {
            // Feck! We be sinking Cap'n!
            $booty = $this->sinkingShip($e);
        }

        $this->response->setBody($booty);
        $this->response->send();
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
        $this->request->setParam('error',$e);
//        $dispatch = new \App\Controller\ErrorController($this->request);
//        $dispatch->errorAction();
//        /** @var \stdClass $view_vars  */
//        $view_vars = (array) $dispatch->view;
//        $view_vars = array_merge($view_vars,array('error' => $e));
//        $view = 'error/error.twig';
//        $response_body = $dispatch->getTwig()->render($view, $view_vars);
//        return $this->templateCheck($dispatch,$response_body);

        // @todo this needs testing manually with bonemvc skeleton
        $this->config['controller_name'] = class_exists('\App\Controller\ErrorController') ? '\App\Controller\ErrorController' : '\Bone\Mvc\Controller';
        $this->config['action_name'] = 'errorAction';
        $this->config['controller'] = 'error';
        $this->config['action'] = 'error';
        $this->controller = new $this->config['controller_name']($this->request);

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
        $this->config['controller_name'] = class_exists('\App\Controller\ErrorController') ? '\App\Controller\ErrorController' : '\Bone\Mvc\Controller';
        $this->config['action_name'] = 'notFoundAction';
        $this->config['controller'] = 'error';
        $this->config['action'] = 'not-found';
        $this->controller = new $this->config['controller_name']($this->request);
    }



}