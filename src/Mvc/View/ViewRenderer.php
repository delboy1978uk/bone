<?php

namespace Bone\Mvc\View;

use Bone\Http\Response;
use Exception;
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

    /** @var string $layout */
    private $layout;

    /**
     * ViewRenderer constructor.
     * @param ViewEngine $viewEngine
     * @param string $layout
     */
    public function __construct(ViewEngine $viewEngine, string $layout)
    {
        $this->viewEngine = $viewEngine;
        $this->layout = $layout;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = $handler->handle($request);
        $data = json_decode($response->getBody(), true);
        $folder = 'src/' . $data['module'].'/View';

        if (is_dir($folder)) {
            $this->viewEngine->addFolder($data['module'], $folder);
        }

        $body = $this->viewEngine->render($data['module'] . '::' . $data['controller'] . '/' . $data['action'], $data['body']);
        $body = $this->viewEngine->render($this->layout, ['content' => $body]);

        $response = new Response();
        $stream = new Stream('php://memory', 'r+');
        $stream->write($body);
        $response = $response->withBody($stream);

        return $response;
    }
}