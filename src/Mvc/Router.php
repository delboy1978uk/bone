<?php

namespace Bone\Mvc;
use Bone\Mvc\Router\Route;
use Bone\Regex;

class Router
{
    private $request;
    private $uri;
    private $controller;
    private $action;
    private $params;
    private $routes;


    /**
     *  We be needin' t' look at th' map
     *  @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->uri = $request->getURI();
        $this->controller = 'index';
        $this->action = 'index';
        $this->params = array();
        $this->routes = array();

        // get th' path 'n' query string from url
        $parse = parse_url($this->uri);
        $this->uri = $parse['path'];

    }


    /**
     *  @return bool
     */
    private function matchCustomRoute()
    {
        /** @var \Bone\Mvc\Router\Route $route */
        foreach($this->routes as $route)
        {
            // if the regex ain't for the home page an' it matches our route
            $strings = $route->getRegexStrings();
            if($strings[0] != '\/' && $matches = $route->checkRoute($this->uri))
            {
                // Garrr me hearties! It be a custom route from th' configgeration!
                $this->controller = $route->getControllerName();
                $this->action = $route->getActionName();
                $this->params = $route->getParams();
                return true;
            }
        }
        return false;
    }


    private function regexMatch($regex_string)
    {
        $regex = new Regex($regex_string);
        return $regex->getMatches($this->uri);
    }

    /**
     * @param array $matches
     */
    private function setController(array $matches)
    {
        $this->action = $matches['controller'];
    }

    /**
     * @param array $matches
     */
    private function setAction(array $matches)
    {
        $this->action = $matches['action'];
    }

    /**
     * @param array $matches
     */
    private function setVarValPairs(array $matches)
    {
        $ex = explode('/',$matches['varvalpairs']);
        for($x = 0; $x <= count($ex)-1 ; $x += 2)
        {
            if(isset($ex[$x+1]))
            {
                $this->params[$ex[$x]] = $ex[$x+1];
            }
        }
    }


    /**
     *  @return bool
     */
    private function matchControllerActionParamsRoute()
    {
        if($matches = $this->regexMatch(Regex\Url::CONTROLLER_ACTION_VARS))
        {
            // we have a controller action var val match Cap'n!
            $this->setController($matches);
            $this->setAction($matches);
            $this->setVarValPairs($matches);
            return true;
        }
        return false;
    }


    /**
     *  @return bool
     */
    private function matchControllerActionRoute()
    {
        if($matches = $this->regexMatch(Regex\Url::CONTROLLER_ACTION))
        {
            // we have a controller action match Cap'n!
            $this->setController($matches);
            $this->setAction($matches);
            return true;
        }
        return false;
    }


    /**
     *  @return bool
     */
    private function matchControllerRoute()
    {
        if($matches = $this->regexMatch(Regex\Url::CONTROLLER))
        {
            // we have a controller action match Cap'n!
            // settin' the destination controller and action and params
            $this->setController($matches);
            $this->action = 'index';
            return true;
        }
        return false;
    }

    /**
     *  gets custom routes from config
     */
    private function setCustomRoutesFromConfig()
    {
        // we be checkin' our instruction fer configgered routes
        $configgeration = Registry::ahoy()->get('routes');

        // stick some voodoo pins in the map
        foreach($configgeration as $route => $options)
        {
            // add the route t' the map
            $this->routes[] = new Route($route,$options);
        }
    }

    /**
     *  Merges params from config
     */
    private function setParams()
    {
        // be addin' the $_GET an' $_POST t' th' params!
        $method = $this->request->getMethod();
        if($method == "POST")
        {
            $this->params = array_merge($this->params, $this->request->getPost());
        }
        $this->params = array_merge($this->params, $this->request->getGet());
    }

    /**
     *  Tells the Navigator to go to the / route
     */
    private function matchRoute()
    {
        // Get th' navigator!
        if(!$this->matchCustomRoute())
        {
            if(!$this->matchControllerActionParamsRoute())
            {
                if(!$this->matchControllerActionRoute())
                {
                    if(!$this->matchControllerRoute())
                    {
                        // there be nay route that matches cap'n
                        // settin' the destination controller and action and params
                        $this->controller = 'error';
                        $this->action = 'not-found';
                    }
                }
            }
        }
    }

    /**
     *  Tells the Navigator to go to the / route
     */
    private function sailHome()
    {
        $routes = Registry::ahoy()->get('routes');
        $home_page = $routes['/'];
        $this->controller = $home_page['controller'];
        $this->action = $home_page['action'];
        $this->params = $home_page['params'];
    }



    /**
     *  Figger out where we be goin'
     */
    private function parseRoute()
    {

        // start at the home page
        $this->sailHome();

        // which way be we goin' ?
        $path = $this->uri;

        // Has th' route been set?
        if ($path != '/')
        {
            // Set the routes configgerd in th' config.php
            $this->setCustomRoutesFromConfig();

            // Match the feckin' route ya blubberin' seadog!
            $this->matchRoute();

            // Merge th' GET POST and config params
            $this->setParams();

            return;
        }
    }


    /**
     *  Garrr, we be makin' sure what they says
     *
     * @return Request
     */
    public function dispatch()
    {
        $this->parseRoute();
        $controller = $this->controller;
        $action = $this->action;

        $this->request->setController($controller);
        $this->request->setAction($action);
        $this->request->setParams($this->params);

        return $this->request;
    }

}