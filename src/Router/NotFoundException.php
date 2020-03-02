<?php declare(strict_types=1);

namespace Bone\Router;

use League\Route\Http\Exception\NotFoundException as RouteException;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends RouteException
{
    /** @var ServerRequestInterface $request */
    private $request;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}