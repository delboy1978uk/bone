<?php

namespace Bone\Mvc\Router\Decorator;

use Bone\Mvc\View\ViewEngine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ExceptionDecorator implements MiddlewareInterface
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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $requestHandler) : ResponseInterface {
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

            $stream = new Stream('php://memory', 'r+');
            $stream->write($body);
            $response = (new Response())->withStatus(404)->withBody($stream);

            return $response;
        }
    }
};

