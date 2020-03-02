<?php

namespace Bone\Router;

use Bone\Router\Decorator\ExceptionDecorator;
use Bone\Router\Decorator\NotAllowedDecorator;
use Bone\Router\Decorator\NotFoundDecorator;
use Bone\View\PlatesEngine;
use Bone\Traits\HasLayoutTrait;
use Exception;
use League\Route\Http\Exception\{MethodNotAllowedException, NotFoundException};
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;

class PlatesStrategy extends ApplicationStrategy implements StrategyInterface
{
    use HasLayoutTrait;

    /** @var PlatesEngine $viewEngine */
    private $viewEngine;

    /** @var NotFoundDecorator $notFoundDecorator */
    private $notFoundDecorator;

    /** @var NotAllowedDecorator $notAllowedDecorator */
    private $notAllowedDecorator;

    /** @var ExceptionDecorator $exceptionDecorator */
    private $exceptionDecorator;

    /**
     * PlatesStrategy constructor.
     * @param PlatesEngine $viewEngine
     * @param NotFoundDecorator $notFound
     * @param NotAllowedDecorator $notAllowed
     * @param string $layout
     */
    public function __construct(PlatesEngine $viewEngine, NotFoundDecorator $notFound, NotAllowedDecorator $notAllowed, string $layout, ExceptionDecorator $exceptionDecorator)
    {
        $this->viewEngine = $viewEngine;
        $this->notFoundDecorator = $notFound;
        $this->notAllowedDecorator = $notAllowed;
        $this->exceptionDecorator = $exceptionDecorator;
        $this->setLayout($layout);
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

            $response = parent::invokeRouteCallable($route, $request);
            $contentType = $response->getHeader('Content-Type');

            if ($contentType && strstr($contentType[0], 'application/json')) {
                return $response;
            }

            $body = ['content' => $response->getBody()->getContents()];
            $body = $this->viewEngine->render($this->layout, $body);

            return $this->getResponseWithBodyAndStatus($response, $body, $response->getStatusCode());

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

            return $this->getResponseWithBodyAndStatus(new HtmlResponse($body), $body, $status);
        }

    }

    /**
     * @param ResponseInterface $response
     * @param string $body
     * @param int $status
     * @return \Psr\Http\Message\MessageInterface|Response
     */
    private function getResponseWithBodyAndStatus(Response $response, string $body, int $status = 200)
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = $response->withStatus($status)->withBody($stream);

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

    /**
     * @return MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->exceptionDecorator;
    }
}