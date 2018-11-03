<?php

namespace Bone\Mvc;

use Bone\Db\Adapter\MySQL;
use Bone\Mvc\View\Extension\Plates\Translate;
use Bone\Mvc\View\ViewEngine;
use Bone\Mvc\View\PlatesEngine;
use Bone\Server\Environment;
use Bone\Service\LoggerFactory;
use Bone\Service\MailService;
use Bone\Service\TranslatorFactory;
use InvalidArgumentException;
use League\Plates\Engine;
use LogicException;
use Monolog\Logger;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Zend\Diactoros\Response\TextResponse;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Controller
{
    /** @var ServerRequestInterface */
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

    /** @var array $headers */
    private $headers;

    /** @var int $statusCode */
    private $statusCode = 200;

    /** @var MailService $mailService */
    private $mailService;

    /** @var array $params */
    public $params;

    /** @var array $post */
    protected $post = [];

    /** @var MySQL */
    protected $_db;

    /** @var Environment $serverEnvironment */
    protected $serverEnvironment;

    /** @var Logger[] $log */
    protected $log;

    protected $translator;

    /**
     * Controller constructor.
     * @param ServerRequestInterface $request
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
     * @return MailService
     */
    public function getMailService()
    {
        if (!$this->mailService instanceof MailService) {
            $this->initMailService();
        }

        return $this->mailService;
    }

    private function initMailService()
    {
        $this->mailService = new MailService();
        $options = Registry::ahoy()->get('mail');
        if (isset($options['name']) && isset($options['host']) && isset($options['port']) ) {
            $transport = new Smtp();
            $options   = new SmtpOptions($options);
            $transport->setOptions($options);
            $this->mailService->setTransport($transport);
        }
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
        return array_merge($this->params, $this->post);
    }

    /**
     * @param $param
     * @return mixed
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
     * @return array
     */
    public function indexAction()
    {
        return ['message' => 'Override this method'];
    }

    public function errorAction()
    {
        $this->disableView();
        $this->disableLayout();
        $this->body = '500 Page Error.';
        return new TextResponse($this->body, 500);
    }

    public function notFoundAction()
    {
        $this->disableView();
        $this->disableLayout();
        $this->body = '404 Page Not Found.';
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
     * @return array|\Monolog\Logger[]
     * @throws \Exception
     */
    public function getLog($channel = 'default')
    {
        if (!$this->log) {
            $this->log = $this->initLogs();
        }

        if (!isset($this->log[$channel])) {
            throw new InvalidArgumentException('No log channel with that name found');
        }

        return $this->log[$channel];
    }

    /**
     * @return array|\Monolog\Logger[]
     * @throws \Exception
     */
    private function initLogs()
    {
        $config = Registry::ahoy()->get('log');
        if (!is_array($config)) {
            throw new LogicException('No log config found');
        }
        $factory = new LoggerFactory();
        $logs = $factory->createLoggers($config);
        return $logs;

    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->translator = $this->initTranslator();
        }

        return $this->translator;
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    private function initTranslator()
    {
        $config = Registry::ahoy()->get('i18n');
        if (!is_array($config)) {
            throw new LogicException('No i18n config found');
        }

        $factory = new TranslatorFactory();
        $translator = $factory->createTranslator($config);

        $engine = $this->getViewEngine();
        if ($engine instanceof Engine) {
            $engine->loadExtension(new Translate($translator));
        }

        return $translator;
    }
}
