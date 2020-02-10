<?php

declare(strict_types=1);

namespace BoneTest\TestPackage\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;

class TestPackageApiController
{
    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     */
    public function indexAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        return new JsonResponse([
            'drink' => 'grog',
            'pieces' => 'of eight',
            'shiver' => 'me timbers',
        ]);
    }
}
