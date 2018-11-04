<?php

namespace Bone\Mvc;

use Bone\Mvc\Router\Route;
use Bone\Regex;
use Psr\Http\Message\ServerRequestInterface;

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
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
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
     * @return bool
     */
    private function matchCustomRoute()
    {
        /** @var \Bone\Mvc\Router\Route $route */
        foreach ($this->routes as $route) {
            // if the regex ain't for the home page an' it matches our route
            $strings = $route->getRegexStrings();
            if ($strings[0] != '\/' && $matches = $route->checkRoute($this->uri)) {
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
        $this->controller = $matches['controller'];
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
        $ex = explode('/', $matches['varvalpairs']);
        for ($x = 0; $x <= count($ex) - 1; $x += 2) {
            if (isset($ex[$x + 1])) {
                $this->params[$ex[$x]] = $ex[$x + 1];
            }
        }
    }


    /**
     * @return array
     */
    private function matchControllerActionParamsRoute()
    {
        return $this->regexMatch(Regex\Url::CONTROLLER_ACTION_VARS);
    }


    /**
     * @return array|null
     */
    private function matchControllerActionRoute()
    {
        return $this->regexMatch(Regex\Url::CONTROLLER_ACTION);
    }


    /**
     * @return array
     */
    private function matchControllerRoute()
    {
        return $this->regexMatch(Regex\Url::CONTROLLER);
    }


    /**
     *  gets custom routes from config
     */
    private function setCustomRoutesFromConfig()
    {
        // we be checkin' our instruction fer configgered routes
        $configgeration = Registry::ahoy()->get('routes');

        // stick some voodoo pins in the map
        foreach ($configgeration as $route => $options) {
            // add the route t' the map
            $this->routes[] = new Route($route, $options);
        }
    }


    /**
     *  Merges params from config
     */
    private function setParams()
    {
        // be addin' the $_GET an' $_POST t' th' params!
        $method = $this->request->getMethod();
        $serverParams = $this->request->getServerParams();
        $queryParams = $this->request->getQueryParams();
        if ($method == "POST") {
            $this->params = array_merge($this->params, $serverParams);
        }
        $this->params = array_merge($this->params, $queryParams);
    }


    /**
     *  Tells the Navigator to go to the / route
     */
    private function matchRoute()
    {
        // we be startin' off assumin' th' voyage will be a disaster
        $this->controller = 'error';
        $this->action = 'not-found';

        // Get th' navigator! Do we know where we be sailin'? Do we have a plan?
        if (!$this->matchCustomRoute()) {
            $this->matchDefaultRoutes();
        }
    }

    public function matchDefaultRoutes()
    {
        if ( $matches = $this->matchControllerActionParamsRoute()) {
            // we have a controller action var val match Cap'n!
            $this->setController($matches);
            $this->setAction($matches);
            $this->setVarValPairs($matches);
        } elseif ($matches = $this->matchControllerActionRoute()) {
            // we have a controller action match Cap'n!
            $this->setController($matches);
            $this->setAction($matches);
        } elseif ($matches = $this->matchControllerRoute()) {
            // we have a controller action match Cap'n!
            // settin' the destination controller and action and params
            $this->setController($matches);
            $this->action = 'index';
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
    public function parseRoute()
    {
        // start at the home page
        $this->sailHome();

        // which way be we goin' ?
        $path = $this->uri;

        // Has th' route been set?
        if ($path != '/') {
            // Set the routes configgerd in th' config.php
            $this->setCustomRoutesFromConfig();

            // Match the feckin' route ya blubberin' seadog!
            $this->matchRoute();
        }

        // Merge th' GET POST and config params
        $this->setParams();
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParams()
    {
        return $this->params;
    }
}