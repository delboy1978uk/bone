<?php

namespace Bone\Mvc\Router;

use Bone\Http\Response as BoneResponse;
use Bone\Mvc\Router\Decorator\ExceptionDecorator;
use Bone\Mvc\Router\Decorator\NotAllowedDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\View\PlatesEngine;
use Bone\Mvc\View\ViewEngine;
use Bone\Mvc\View\ViewRenderer;
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
    /** @var PlatesEngine $viewEngine */
    private $viewEngine;

    /** @var NotFoundDecorator $notFoundDecorator */
    private $notFoundDecorator;

    /** @var NotAllowedDecorator $notAllowedDecorator */
    private $notAllowedDecorator;

    /** @var ExceptionDecorator $exceptionDecorator\ */
    private $exceptionDecorator;

    public function __construct(PlatesEngine $viewEngine, ExceptionDecorator $exception, NotFoundDecorator $notFound, NotAllowedDecorator $notAllowed)
    {
        $this->viewEngine = $viewEngine;
        $this->exceptionDecorator = $exception;
        $this->notFoundDecorator = $notFound;
        $this->notAllowedDecorator = $notAllowed;
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
//        try {
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
            $body = json_encode([
                'body' => $body,
                'module' => $module,
                'controller' => $controller,
                'action' => $action,
            ]);
            $stream = new Stream('php://memory', 'r+');
            $stream->write($body);
            return $response->withBody($stream);

//        } catch (Exception $e) {
//            $body = $this->viewEngine->render('error/error', [
//                'message' => $e->getMessage(),
//                'code' => $e->getCode(),
//                'trace' => $e->getTrace(),
//            ]);
//            $body = $this->viewEngine->render('layouts/layout', [
//                'content' => $body,
//            ]);
//
//            $stream = new Stream('php://memory', 'r+');
//            $stream->write($body);
//            $response = (new Response())->withStatus(500)->withBody($stream);
//
//            return $response;
//        }

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

    private function getErrorResponse(string $body, int $code = 500)
    {
        $body = $this->viewEngine->render('layouts/layout', [
            'content' => $body,
        ]);
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = (new Response())->withStatus($code)->withBody($stream);

        return $response;
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

    /**
     * Get a middleware that acts as an exception handler, it should wrap the rest of the
     * middleware stack and catch eny exceptions.
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->exceptionDecorator;
    }

}