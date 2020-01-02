<?php

namespace BoneTest\Mvc\Controller;

use Bone\Exception;
use Bone\Mvc\Controller\DownloadController;
use Bone\Mvc\View\Extension\Plates\AlertBox;
use Codeception\TestCase\Test;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest;

class DownloadControllerTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testControllerThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new DownloadController('some_folder');
    }

    public function testControllerThrows404()
    {
        $this->expectException(Exception::class);
        $controller = new DownloadController('tests/_data');
        $request = new ServerRequest();
        $request = $request->withQueryParams(['file' => '/nothing.png']);
        $controller->downloadAction($request, []);
    }

    public function testDownloadActionThrows4009()
    {
        $this->expectException(Exception::class);
        $controller = new DownloadController('tests/_data');
        $request = new ServerRequest();
        $request = $request->withQueryParams(['oops' => '/nothing.png']);
        $controller->downloadAction($request, []);
    }

    public function testController()
    {
        $controller = new DownloadController('tests/_data');
        $request = new ServerRequest();
        $request = $request->withQueryParams(['file' => '/skull_and_crossbones.png']);
        $this->assertInstanceOf(ResponseInterface::class, $controller->downloadAction($request, []));
    }

    public function testControllerPublicAsset()
    {
        mkdir('public');
        copy('tests/_data/skull_and_crossbones.png', 'public/skull_and_crossbones.png');
        $controller = new DownloadController('tests/_data');
        $request = new ServerRequest();
        $request = $request->withQueryParams(['file' => '/skull_and_crossbones.png']);
        $this->assertInstanceOf(ResponseInterface::class, $controller->downloadAction($request, []));
        unlink('public/skull_and_crossbones.png');
        rmdir('public');
    }
}