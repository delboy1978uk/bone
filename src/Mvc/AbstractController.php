<?php

namespace Bone\Mvc;

use Bone\Db\Adapter\MySQL;
use Bone\Mvc\View\PlatesEngine;
use Bone\Mvc\View\ViewEngine;
use Bone\Server\Environment;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController
{
    /** @var ServerRequestInterface */
    protected $request;

    /** @var ViewEngine $plates */
    protected $viewEngine;

    /** @var string $controller */
    protected $controller;

    /** @var string $action */
    protected $action;

    /** @var stdClass $view */
    public $view;

    /** @var string $body */
    private $body;

    /** @var bool */
    private $layoutEnabled;

    /** @var bool */
    private $viewEnabled;

    /** @var array $headers */
    private $headers;

    /** @var int $statusCode */
    private $statusCode = 200;

    /** @var array $params */
    public $params;

    /** @var array $post */
    protected $post = [];

    /**
     * Controller constructor.
     * @param ServerRequestInterface $request
     * @throws \Exception
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->headers = [];
        $this->params = $this->request->getQueryParams();

        if ($this->request->getMethod() == 'POST') {
            $body = $this->request->getParsedBody();
            $this->post = $body ?: [];
        }

        $this->initViewEngine();
        $this->view = new stdClass();
        $this->layoutEnabled = true;
        $this->viewEnabled = true;
    }

    public function getParams()
    {
        return array_merge($this->params, $this->post);
    }

    /**
     * @param $param
     * @param null $default
     * @return mixed|null|string
     */
    public function getParam($param, $default = null)
    {
        $set = isset($this->params[$param]);
        if ($set && is_string($this->params[$param])) {
            return urldecode($this->params[$param]);
        } elseif ($set) {
            return $this->params[$param];
        }
        return $default;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function setParam($key, $val)
    {
        $this->params[$key] = $val;
        return $this;
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

    public function setViewEngine(ViewEngine $engine)
    {
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
     *  For loadin' th' cannon, so t' speak
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return bool
     */
    public function hasLayoutEnabled()
    {
        return ($this->layoutEnabled === true);
    }

    /**
     * Enables the layout
     */
    public function enableLayout()
    {
        $this->layoutEnabled = true;
    }

    /**
     * Disables the layout
     */
    public function disableLayout()
    {
        $this->layoutEnabled = false;
    }

    /**
     * @return bool
     */
    public function hasViewEnabled()
    {
        return ($this->viewEnabled === true);
    }

    /**
     * Enables the view
     */
    public function enableView()
    {
        $this->viewEnabled = true;
    }

    /**
     * Disables the view
     */
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

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * @param $key
     * @return string|null
     */
    public function getHeader($key)
    {
        return $this->headers[$key] ? $this->headers[$key] : null;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Controller
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param array $data
     * @param int $statusCode
     */
    public function sendJsonResponse(array $data, $statusCode = 200)
    {
        $this->disableLayout();
        $this->disableView();
        $this->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->setHeader('Expires','Mon, 26 Jul 1997 05:00:00 GMT');
        $this->setHeader('Content-Type','application/json');
        $json = json_encode($data);
        $this->setBody($json);
        $this->setStatusCode($statusCode);
    }

    /**
     * @param null $key
     * @param string $default
     * @return array|string|null
     */
    public function getPost($key = null, $default = null)
    {
        if ($key) {
            return array_key_exists($key, $this->post) ? $this->post[$key] : $default;
        }

        return $this->post;
    }

    /**
     * @return Environment
     */
    public function getServerEnvironment(): Environment
    {
        return $this->serverEnvironment;
    }

    /**
     * @param Environment $serverEnvironment
     */
    public function setServerEnvironment(Environment $serverEnvironment)
    {
        $this->serverEnvironment = $serverEnvironment;
    }

    /**
     * @return ViewEngine
     */
    public function getViewEngine()
    {
        return $this->viewEngine;
    }

}