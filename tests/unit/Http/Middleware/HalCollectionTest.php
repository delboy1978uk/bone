<?php

namespace BoneTest\Http\Middleware;

use Bone\Http\Middleware\HalCollection;
use Codeception\TestCase\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class HalCollectionTest extends Test
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
                return new JsonResponse([
                    '_embedded' => [
                        [
                            'id' => 1,
                            'drink' => 'grog'
                        ], [
                            'id' => 1,
                            'yo ho ho' => 'bottle of rum'
                        ], [
                            'id' => 3,
                            'shiver me' => 'timbers'
                        ],
                    ],
                    'total' => 4,
                ]);
            }
        };
    }

    /**
     * @throws \Exception
     */
    public function testProcesss()
    {
        $request = new ServerRequest([], [], new Uri('https://awesome.scot'));
        $halEntityMiddleware = new HalCollection(3);
        $response = $halEntityMiddleware->process($request, $this->fakeRequestHandler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $body = $response->getBody();
        $body->rewind();
        $json = $body->getContents();
        $this->assertEquals('{"_links":{"self":{"href":"https:\/\/awesome.scot"},"first":{"href":"https:\/\/awesome.scot"},"next":{"href":"https:\/\/awesome.scot?page=2"},"last":{"href":"https:\/\/awesome.scot?page=2"}},"_embedded":[{"id":1,"drink":"grog","_links":{"self":{"href":"https:\/\/awesome.scot\/1"}}},{"id":1,"yo ho ho":"bottle of rum","_links":{"self":{"href":"https:\/\/awesome.scot\/1"}}},{"id":3,"shiver me":"timbers","_links":{"self":{"href":"https:\/\/awesome.scot\/3"}}}],"total":4}', $json);
    }
}