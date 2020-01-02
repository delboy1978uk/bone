<?php

use Bone\Mvc\Router\NotFoundException;
use Codeception\TestCase\Test;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;

class NotFoundExceptionTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSet()
    {
        $e = new NotFoundException();
        $request = new ServerRequest();
        $e->setRequest($request);
        $this->assertInstanceOf(ServerRequestInterface::class, $e->getRequest());
    }
}