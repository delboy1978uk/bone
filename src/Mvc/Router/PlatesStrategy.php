<?php

namespace Bone\Mvc\Router;

use Bone\Http\Response as BoneResponse;
use Bone\Mvc\Router\Decorator\ExceptionDecorator;
use Bone\Mvc\Router\Decorator\NotAllowedDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\View\PlatesEngine;
use Bone\Mvc\View\ViewEngine;
use Bone\Mvc\View\ViewRenderer;
use Bone\Server\I18nHandler;
use Bone\Traits\LayoutAwareTrait;
use Exception;
use League\Route\Http\Exception\{MethodNotAllowedException, NotFoundException};
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class PlatesStrategy extends ApplicationStrategy implements StrategyInterface
{
    use LayoutAwareTrait;

    /** @var PlatesEngine $viewEngine */
    private $viewEngine;

    /** @var NotFoundDecorator $notFoundDecorator */
    private $notFoundDecorator;

    /** @var NotAllowedDecorator $notAllowedDecorator */
    private $notAllowedDecorator;

    /** @var bool  */
    private $i18nEnabled = false;

    /** @var array  */
    private $supportedLocales = [];

    /**
     * PlatesStrategy constructor.
     * @param PlatesEngine $viewEngine
     * @param NotFoundDecorator $notFound
     * @param NotAllowedDecorator $notAllowed
     * @param string $layout
     */
    public function __construct(PlatesEngine $viewEngine, NotFoundDecorator $notFound, NotAllowedDecorator $notAllowed, string $layout)
    {
        $this->viewEngine = $viewEngine;
        $this->notFoundDecorator = $notFound;
        $this->notAllowedDecorator = $notAllowed;
        $this->setLayout($layout);
    }

    /**
     * @param bool $i18nEnabled
     */
    public function setI18nEnabled(bool $i18nEnabled): void
    {
        $this->i18nEnabled = $i18nEnabled;
    }

    /**
     * @param array $locales
     */
    public function setSupportedLocales(array $locales): void
    {
        $this->supportedLocales = $locales;
    }


    /**
     * Invoke the route callable based on the strategy.
     *
     * @param \League\Route\Route $route
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        try {

            $controller = $route->getCallable($this->container);
            $controllerClass = get_class($controller[0]);
            $actionMethod = $controller[1];
            if (preg_match('#(?<module>\w+)\\\Controller\\\(?<controller>\w+)Controller$#', $controllerClass, $matches)) {
                $module = $matches['module'];
                $controller = $matches['controller'];
            }

            if (preg_match('#(?<action>\w+)Action#', $actionMethod, $action)) {
                $action = $action['action'];
            }
            $response = parent::invokeRouteCallable($route, $request);

            $body = json_decode($response->getBody(), true);

            $folder = 'src/' . $module.'/View';

            if (is_dir($folder)) {
                $this->viewEngine->addFolder($module, $folder);
            }

            $viewName = $module . '::' . $controller . '/' . $action;
            $body = $this->viewEngine->render($viewName, $body);
            $body = $this->viewEngine->render($this->layout, ['content' => $body]);

            return $this->getResponseWithBodyAndStatus($body, 200);

        } catch (Exception $e) {
            $body = $this->viewEngine->render('error/error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);
            $body = $this->viewEngine->render($this->layout, [
                'content' => $body,
            ]);
            $status = ($e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;

            return $this->getResponseWithBodyAndStatus($body, $status);
        }

    }

    private function getResponseWithBodyAndStatus(string $body, int $status = 200)
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = (new Response())->withStatus($status)->withBody($stream);

        return $response;
    }

    /**
     * Get a middleware that will decorate a NotFoundException
     *
     * @param \League\Route\Http\Exception\NotFoundException $exception
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    public function getNotFoundDecorator(NotFoundException $e): MiddlewareInterface
    {
        return $this->notFoundDecorator;
    }

    /**
     * Get a middleware that will decorate a NotAllowedException
     *
     * @param \League\Route\Http\Exception\NotFoundException $e
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $e): MiddlewareInterface
    {
        return $this->notAllowedDecorator;
    }
}