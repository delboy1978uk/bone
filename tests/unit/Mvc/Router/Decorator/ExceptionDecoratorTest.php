<?php

namespace BoneTest\Mvc\Router\Decorator;

use Bone\Router\Decorator\ExceptionDecorator;
use Bone\View\ViewEngine;
use Codeception\TestCase\Test;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ServerRequest;

class ExceptionDecoratorTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testDecoreator()
    {
        $view = new ViewEngine('tests/_data/view');
        $decorator = new ExceptionDecorator($view, [
            'exception' => 'error/error',
            401 => 'error/not-authorised',
            403 => 'error/not-authorised',
            404 => 'error/not-found',
            405 => 'error/not-allowed',
            500 => 'error/error',
        ]);
        $decorator->setLayout('layouts/bone');
        $request = new ServerRequest();
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new Exception();
            }
        };
        $this->assertInstanceOf(ResponseInterface::class, $decorator->process($request, $handler));
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new Exception('argh', 500);
            }
        };
        $this->assertInstanceOf(ResponseInterface::class, $decorator->process($request, $handler));
    }
}