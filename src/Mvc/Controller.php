<?php

namespace Bone\Mvc;

use Bone\Db\Adapter\MySQL;
use Bone\Mvc\Response\Headers;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;
use stdClass;

class Controller
{
    /**
     * @var Request
     */
    protected $_request;

    protected $_twig;

    protected $controller;

    protected $action;

    private $headers;

    public $view;

    private $body;

    /**
     * @var bool
     */
    private $layout_enabled;

    /**
     * @var bool
     */
    private $view_enabled;



    /**
     * @var \Bone\Db\Adapter\MySQL
     */
    protected $_db;

    public function __construct(Request $request)
    {
        $this->_request = $request;
        $this->setTwig();
        $this->headers = new Headers();
        $this->view = new stdClass();
        $this->layout_enabled = true;
        $this->view_enabled = true;
    }

    /**
     * @return null
     */
    protected function setDB()
    {
        $config = Registry::ahoy()->get('db');
        $this->_db = new MySQL($config);
    }

    /**
     * @return null
     */
    protected function setTwig()
    {
        $view_path = file_exists(APPLICATION_PATH.'/src/App/View/') ? APPLICATION_PATH.'/src/App/View/' : '.' ;
        $loader = new Twig_Loader_Filesystem($view_path);
        $this->_twig = new Twig_Environment($loader,array('debug' => true));
        $this->_twig->addExtension(new Twig_Extension_Debug());
    }

    /**
     * @return \PDO
     */
    public function getDbAdapter()
    {
        if(!$this->_db)
        {
            $this->setDB();
        }
        return $this->_db->getConnection();
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->_twig;
    }


    /**
     *  runs before th' controller action
     */
    public function init()
    {
        // extend this t' initialise th' controller
    }

    public function getParams()
    {
        return (object) $this->_request->getParams();
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getParam($param)
    {
        return $this->_request->getParam($param);
    }

    /**
     *  runs after yer work is done
     */
    public function postDispatch()
    {
        // extend this t' run code after yer controller is finished
    }

    /**
     *  For loadin' th' cannon, so t' speak
     *
     * @return Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasLayoutEnabled()
    {
        return ($this->layout_enabled === true);
    }

    public function enableLayout()
    {
        $this->layout_enabled = true;
    }

    public function disableLayout()
    {
        $this->layout_enabled = false;
    }

    public function hasViewEnabled()
    {
        return ($this->view_enabled === true);
    }

    public function enableView()
    {
        $this->view_enabled = true;
    }

    public function disableView()
    {
        $this->view_enabled = false;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *  Only used if Layout & View disabled
     *
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

}