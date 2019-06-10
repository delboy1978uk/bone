<?php

namespace Bone\Mvc\View;

use Bone\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Stream;

class ViewRenderer implements MiddlewareInterface
{
    /**
     * @var ViewEngine
     */
    private $viewEngine;

    public function __construct(ViewEngine $viewEngine)
    {
        $this->viewEngine = $viewEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = $handler->handle($request);
        $data = json_decode($response->getBody(), true);

        $body = $this->viewEngine->render($data['controller'] . '/' . $data['action'], $data['body']);

        $response = new Response();
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = $response->withBody($stream);


        return $response;
    }
}