<?php

namespace Bone\Mvc;

use Bone\Db\Adapter\MySQL;
use Bone\Mvc\View\ViewEngine;
use Bone\Mvc\View\PlatesEngine;
use PDO;
use Psr\Http\Message\RequestInterface;;
use stdClass;

class Controller
{
    /** @var RequestInterface */
    protected $request;

    /** @var ViewEngine $plates */
    protected $viewEngine;

    /** @var string $controller */
    protected $controller;

    /** @var string $action  */
    protected $action;

    /** @var stdClass $view */
    public $view;

    /** @var string $body */
    private $body;

    /** @var bool */
    private $layoutEnabled;

    /** @var bool */
    private $viewEnabled;



    /**
     * @var \Bone\Db\Adapter\MySQL
     */
    protected $_db;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->params = (object) $this->request->getQueryParams();

        $this->initViewEngine();
        $this->view = new stdClass();
        $this->layoutEnabled = true;
        $this->viewEnabled = true;
    }

    /**
     * @return void
     */
    protected function setDB()
    {
        $config = Registry::ahoy()->get('db');
        $this->_db = new MySQL($config);
    }

    /**
     * @return void
     */
    protected function initViewEngine()
    {
        $viewPath = file_exists(APPLICATION_PATH.'/src/App/View/') ? APPLICATION_PATH.'/src/App/View/' : '.' ;
        $engine = new PlatesEngine($viewPath);
        $this->viewEngine = $engine;
    }

    /**
     * @return PDO
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
     * @return ViewEngine
     */
    public function getViewEngine()
    {
        return $this->viewEngine;
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
        return (object) $this->params;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getParam($param)
    {
        $params = $this->getParams();
        return isset($params->$param) ? $params->$param : null;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function setParam($key, $val)
    {
        $this->params->$key = $val;
        return $this;
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
     * @return array
     */
    public function getHeaders()
    {
        return $this->request->getHeaders();
    }

    public function hasLayoutEnabled()
    {
        return ($this->layoutEnabled === true);
    }

    public function enableLayout()
    {
        $this->layoutEnabled = true;
    }

    public function disableLayout()
    {
        $this->layoutEnabled = false;
    }

    public function hasViewEnabled()
    {
        return ($this->viewEnabled === true);
    }

    public function enableView()
    {
        $this->viewEnabled = true;
    }

    public function disableView()
    {
        $this->viewEnabled = false;
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

    private function errorAction()
    {
        $this->disableView();
        $this->disableLayout();
        $this->body = '500 Page Error.';
    }

    private function notFoundAction()
    {
        $this->disableView();
        $this->disableLayout();
        $this->body = '404 Page Not Found.';
    }
}