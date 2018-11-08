<?php

namespace Bone\Mvc;

use Bone\Mvc\View\Extension\Plates\Translate;
use Bone\Service\LoggerFactory;
use Bone\Service\MailService;
use Bone\Service\TranslatorFactory;
use InvalidArgumentException;
use League\Plates\Engine;
use LogicException;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\TextResponse;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class Controller extends AbstractController
{
    /** @var MailService $mailService */
    private $mailService;

    /** @var Logger[] $log */
    protected $log;

    protected $translator;

    /**
     * Controller constructor.
     * @param ServerRequestInterface $request
     * @throws \Exception
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);
        $this->initTranslator();
        $this->initLogs();
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
     *  runs before th' controller action
     */
    public function init()
    {
        // extend this t' initialise th' controller
    }

    /**
     *  runs after yer work is done
     */
    public function postDispatch()
    {
        // extend this t' run code after yer controller is finished
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
        $this->setBody('500 Page Error.');
        return new TextResponse($this->getBody(), 500);
    }

    public function notFoundAction()
    {
        $this->disableView();
        $this->disableLayout();
        $this->setBody('404 Page Not Found.');
    }

    /**
     * @param string $channel
     * @return Logger
     */
    public function getLog($channel = 'default'): Logger
    {
        if (!$this->log) {
            throw new LogicException('No log config found');
        }

        if (!isset($this->log[$channel])) {
            throw new InvalidArgumentException('No log channel with that name found');
        }

        return $this->log[$channel];
    }

    /**
     * @throws \Exception
     */
    private function initLogs()
    {
        $config = Registry::ahoy()->get('log');
        if (is_array($config)) {
            $factory = new LoggerFactory();
            $logs = $factory->createLoggers($config);
            $this->log = $logs;
        }
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            throw new LogicException('No i18n config found');
        }

        return $this->translator;
    }

    private function initTranslator()
    {
        $config = Registry::ahoy()->get('i18n');
        if (is_array($config) && !$this->translator) {

            $factory = new TranslatorFactory();
            $translator = $factory->createTranslator($config);

            $engine = $this->getViewEngine();
            if ($engine instanceof Engine) {
                $engine->loadExtension(new Translate($translator));
            }

            $defaultLocale = $config['default_locale'] ?: 'en_GB';
            $locale = $this->getParam('locale', $defaultLocale);
            if (!in_array($locale, $config['supported_locales'])) {
                $locale = $defaultLocale;
            }
            $translator->setLocale($locale);

            $this->translator = $translator;
        }
    }
}
