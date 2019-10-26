<?php

namespace Bone\Mvc\Router\Decorator;

use Bone\Mvc\View\ViewEngine;
use Bone\Traits\HasLayoutTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class NotFoundDecorator extends ExceptionDecorator implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $this->viewEngine->render('error/not-found');
        $body = $this->viewEngine->render($this->getLayout(), [
            'content' => $body,
        ]);

        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = (new Response())->withStatus(404)->withBody($stream);

        return $response;
    }
}