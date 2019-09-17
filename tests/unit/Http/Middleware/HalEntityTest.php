<?php

namespace BoneTest\Http\Middleware;

use Bone\Http\Middleware\HalEntity;
use Codeception\TestCase\Test;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response\JsonResponse;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

class HalEntityTest extends Test
{
    /**
     * @var \UnitTester
     */

    /**
     * @throws \Exception
     */
    public function testProcesss()
    {
        $request = new Request();
        $response = new JsonResponse(['drink' => 'grog', 'yo ho ho' => 'bottle of rum']);
        $handler = $this->make(RequestHandlerRunner::class, ['handle' => $response]);
        $halEntityMiddleware = new HalEntity();
        $response = $halEntityMiddleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
//        echo $response->getBody()->getContents(); die;
    }
}