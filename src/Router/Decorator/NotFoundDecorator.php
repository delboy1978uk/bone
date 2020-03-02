<?php

namespace Bone\Router\Decorator;

use Bone\View\ViewEngine;
use Bone\Traits\HasLayoutTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

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

        return $this->getResponseWithBodyAndStatus(new Response\HtmlResponse(''), $body, 404);
    }
}