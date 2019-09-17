<?php

namespace Bone\Mvc\Router\Decorator;

use Bone\Mvc\View\ViewEngine;
use Bone\Traits\LayoutAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class NotFoundDecorator implements MiddlewareInterface
{
    use LayoutAwareTrait;


    /** @var ViewEngine  */
    private $viewEngine;

    /** @var string $view */
    private $view;

    /**
     * NotFoundDecorator constructor.
     * @param ViewEngine $viewEngine
     */
    public function __construct(ViewEngine $viewEngine)
    {
        $this->viewEngine = $viewEngine;
        $this->view = 'error/not-found';
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
        $body = $this->viewEngine->render($this->view);
        $body = $this->viewEngine->render($this->getLayout(), [
            'content' => $body,
        ]);

        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = (new Response())->withStatus(404)->withBody($stream);

        return $response;
    }
}