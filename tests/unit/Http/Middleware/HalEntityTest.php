<?php

namespace BoneTest\Http\Middleware;

use Bone\Http\Middleware\HalEntity;
use Codeception\TestCase\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;

class HalEntityTest extends Test
{
    private $fakeRequestHandler;

    public function before()
    {
        $this->fakeRequestHandler = new class implements RequestHandlerInterface {
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
        $halEntityMiddleware = new HalEntity();
        $response = $halEntityMiddleware->process($request, $this->fakeRequestHandler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
//        echo $response->getBody()->getContents(); die;
    }
}