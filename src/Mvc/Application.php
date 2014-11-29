<?php


namespace Bone\Mvc;


class Application
{
    private $registry;

    /**
     *  There be nay feckin wi' constructors on board this ship
     *  There be nay copyin' o' th'ship either
     *  This ship is a singleton!
     */
    public function __construct(){}
    public function __clone(){}


    /**
     *  Ahoy! There nay be boardin without yer configuration
     *
     * @param array $config
     * @return Application
     */
    public static function ahoy(array $config)
    {
        static $inst = null;
        if($inst === null)
        {
            $inst = new Application();
            $inst->registry = Registry::ahoy();
            foreach($config as $key => $value)
            {
                $inst->registry->set($key,$value);
            }
        }
        return $inst;
    }

    /**
     *  T' the high seas! Garrr!
     */
    public function setSail()
    {
        $request = new Request($_GET,$_POST,$_COOKIE,$_SERVER);
        $router = new Router($request);
        $response = new Response();
        $dispatcher = new Dispatcher($router->dispatch(),$response);
        $dispatcher->fireCannons();
    }



}