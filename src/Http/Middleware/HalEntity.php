<?php

namespace Bone\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HalEntity implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();

        $hal = [
            '_links' => [
                'self' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath(),
                ]
            ],
        ];

        $response = $handler->handle($request);

        $data = \json_decode($response->getBody()->getContents(), true);
        $data = \array_merge($hal, $data);

        $body = $response->getBody();
        $body->rewind();
        $body->write(\json_encode($data));

        return $response->withBody($body);
    }
}