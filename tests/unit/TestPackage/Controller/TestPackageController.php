<?php declare(strict_types=1);

namespace BoneTest\TestPackage\Controller;

use Bone\Mvc\View\ViewEngine;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class TestPackageController
{
    /** @var ViewEngine $view */
    private $view;

    public function __construct(ViewEngine $view)
    {
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface $response
     * @throws Exception
     */
    public function indexAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $body = $this->view->render('testpackage::index', []);

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface $response
     * @throws Exception
     */
    public function anotherAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        return new JsonResponse([
            'drink' => 'grog',
            'pieces' => 'of eight',
            'shiver' => 'me timbers',
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface $response
     * @throws Exception
     */
    public function badAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        throw new Exception('Garrrr! It be the redcoats!');
    }
}
