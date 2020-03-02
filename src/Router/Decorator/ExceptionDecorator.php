<?php

namespace Bone\Router\Decorator;

use Bone\View\ViewEngine;
use Bone\Traits\HasLayoutTrait;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Stream;

class ExceptionDecorator implements MiddlewareInterface
{

    use HasLayoutTrait;

    /** @var ViewEngine  */
    protected $viewEngine;

    /** @var string $view */
    protected $view;

    /**
     * @var array $templates
     */
    protected $templates;

    /**
     * ExceptionDecorator constructor.
     * @param ViewEngine $viewEngine
     * @param array $templates
     */
    public function __construct(ViewEngine $viewEngine, array $templates)
    {
        $this->viewEngine = $viewEngine;
        $this->templates = $templates;
        $this->setView('error/error');
    }

    /**
     * @param string $view
     */
    protected function setView(string $view)
    {
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Exception $e) {

            $template = $this->view;
            $code = $e->getCode();

            if (array_key_exists($code, $this->templates)) {
                $template = $this->templates[$code];
            } elseif (array_key_exists('exception', $this->templates)) {
                $template = $this->templates['exception'];
            }

            $body = $this->viewEngine->render($template, [
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
    protected function getResponseWithBodyAndStatus(ResponseInterface $response, string $body, int $status = 200)
    {
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = $response->withStatus($status)->withBody($stream);

        return $response;
    }
}