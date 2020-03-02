<?php

namespace Bone\Router\Decorator;

use Bone\View\ViewEngine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class NotAllowedDecorator extends ExceptionDecorator
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $this->viewEngine->render('error/not-allowed');
        $body = $this->viewEngine->render($this->getLayout(), [
            'content' => $body,
        ]);

        return $this->getResponseWithBodyAndStatus(new HtmlResponse(''), $body, 405);

        return parent::process($request, $handler);
    }
}