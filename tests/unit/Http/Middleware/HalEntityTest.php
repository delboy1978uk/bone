<?php

namespace BoneTest\Http\Middleware;

use Bone\Http\Middleware\HalEntity;
use Codeception\TestCase\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class HalEntityTest extends Test
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