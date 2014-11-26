<?php

namespace Bone\Mvc;

use Bone\Filter;

class Response
{
    private $headers;

    /** @var mixed */
    private $body;

    // Garrrr! An arrrray!
    private $config = array();


    /**
     *  Load the cannons darn ye!
     *
     * @param Request $request controller
     */
    public function __construct(Request $request)
    {


        // what controller be we talkin about?
        $filtered = Filter::filterString($request->getController(),'DashToCamelCase');
        $this->config['controller_name'] = '\App\Controller\\'.ucwords($filtered).'Controller';

        // whit be yer action ?
        $filtered = Filter::filterString($request->getAction(),'DashToCamelCase');
        $this->config['action_name'] = $filtered.'Action';
        $this->config['controller'] = $request->getController();
        $this->config['action'] = $request->getAction();

        // can we find th' darned controller?
        if(!class_exists($this->config['controller_name']))
        {
            $this->config['controller_name'] = '\App\Controller\ErrorController';
            $this->config['action_name'] = 'notFoundAction';
            $this->config['controller'] = 'error';
            $action = 'not-found';
            $dispatch = new $this->config['controller_name']($request);
        }
        else
        {
            $dispatch = new $this->config['controller_name']($request);
            if(!method_exists($dispatch,$this->config['action_name']))
            {
                $this->config['controller_name'] = '\App\Controller\ErrorController';
                $this->config['action_name'] = 'notFoundAction';
                /** @var Controller $dispatch  */
                $dispatch = new $this->config['controller_name']($request);
                $this->config['controller'] = 'error';
                $action = 'not-found';
            }
        }

        try
        {
            // run th' controller action
            $action = $this->config['action_name'];
            $dispatch->init();
            $dispatch->$action();
            $dispatch->postDispatch();

            $this->headers = $dispatch->getHeaders();

            /** @var \stdClass $view_vars  */
            $view_vars = (array) $dispatch->view;

            if($dispatch->hasViewEnabled())
            {
                $view = $this->config['controller'].'/'.$this->config['action'].'.twig';
                $response_body = $dispatch->getTwig()->render($view, $view_vars);
            }
            else
            {
                $response_body = $dispatch->getBody();
            }
            if($dispatch->hasLayoutEnabled())
            {

                //check we be usin' th' templates in th' config
                $templates = Registry::ahoy()->get('templates');
                $template = ($templates != null) ? $templates[0] : null;
                if($template)
                {
                    $response_body = $dispatch->getTwig()->render('layouts/'.$template.'.twig',array('content' => $response_body));
                }
            }


        }
        catch(Exception $e)
        {
            $request->setParam('error',$e);
            $dispatch = new \App\Controller\ErrorController($request);
            $dispatch->errorAction();
            /** @var \stdClass $view_vars  */
            $view_vars = (array) $dispatch->view;
            $view_vars = array_merge($view_vars,array('error' => $e));
            $view = 'error/error.twig';
            $response_body = $dispatch->getTwig()->render($view, $view_vars);
            //check we be usin' th' templates in th' config
            $templates = Registry::ahoy()->get('templates');
            $template = ($templates != null) ? $templates[0] : null;
            if($template)
            {
                $response_body = $dispatch->getTwig()->render('layouts/'.$template.'.twig',array('content' => $response_body));
            }
        }

        $this->body = $response_body;
    }


    /**
     *  Fire th' Cannons!!
     *
     * @return string
     */
    public function send()
    {
        $this->headers->dispatch();
        echo $this->body;
    }
}