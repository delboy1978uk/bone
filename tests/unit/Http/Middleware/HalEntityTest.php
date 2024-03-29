<?php

namespace BoneTest\Http\Middleware;

use Bone\Http\Middleware\HalEntity;
use Codeception\Test\Unit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;

class HalEntityTest extends Unit
{
    private $fakeRequestHandler;

    public function _before()
    {
        $this->fakeRequestHandler = new class implements RequestHandlerInterface {
            /**
             * @param ServerRequestInterface $request
             * @return ResponseInterface
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new JsonResponse(['drink' => 'grog', 'yo ho ho' => 'bottle of rum']);
            }
        };
    }

    /**
     * @throws \Exception
     */
    public function testProcesss()
    {
        $request = new ServerRequest();
        $request = $request->withUri(new Uri('https://awesome.scot'));
        $halEntityMiddleware = new HalEntity();
        $response = $halEntityMiddleware->process($request, $this->fakeRequestHandler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $body = $response->getBody();
        $body->rewind();
        $json = $body->getContents();
        $this->assertEquals('{"_links":{"self":{"href":"https:\/\/awesome.scot"}},"drink":"grog","yo ho ho":"bottle of rum"}', $json);
    }
}
