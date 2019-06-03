<?php

namespace Bone\Mvc\Router;

use Bone\Mvc\View\PlatesEngine;
use Bone\Mvc\View\ViewEngine;
use Exception;
use League\Route\Http\Exception\{MethodNotAllowedException, NotFoundException};
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class PlatesStrategy extends ApplicationStrategy implements StrategyInterface
{
    private $viewEngine;

    public function __construct()
    {
        $this->viewEngine = new PlatesEngine('src/App/View');
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
            return parent::invokeRouteCallable($route, $request);
        } catch (Exception $e) {
            $body = $this->viewEngine->render('error/error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);
            $body = $this->viewEngine->render('layouts/layout', [
                'content' => $body,
            ]);
            die($body);
        }

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
        $body = $this->viewEngine->render('error/not-found', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTrace(),
        ]);
        $body = $this->viewEngine->render('layouts/layout', [
            'content' => $body,
        ]);
        die($body);
        return parent::getNotFoundDecorator($e);
    }

    /**
     * Get a middleware that will decorate a NotAllowedException
     *
     * @param \League\Route\Http\Exception\NotFoundException $exception
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface
    {
        $body = $this->viewEngine->render('error/error', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTrace(),
        ]);
        $body = $this->viewEngine->render('layouts/layout', [
            'content' => $body,
        ]);
        die($body);
        return parent::getNotFoundDecorator($e);
    }

    /**
     * Get a middleware that acts as an exception handler, it should wrap the rest of the
     * middleware stack and catch eny exceptions.
     *
     * @return \Psr\Http\Server\MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        $view = $this->viewEngine;
        return new class ($view) implements MiddlewareInterface
        {
            /**
             * @var ViewEngine
             */
            private $viewEngine;

            public function __construct(ViewEngine $view)
            {
                $this->viewEngine = $view;
            }

            /**
             * {@inheritdoc}
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ) : ResponseInterface {
                try {
                    return $requestHandler->handle($request);
                } catch (Throwable $e) {
                    $body = $this->viewEngine->render('error/error', [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'trace' => $e->getTrace(),
                    ]);
                    $body = $this->viewEngine->render('layouts/layout', [
                        'content' => $body,
                    ]);
                }
            }
        };
    }

    public function embedContentInLayout(string $content)
    {
        return '';
    }

}