<?php declare(strict_types=1);

namespace BoneTest\Http\RequestHandler;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class I18nTestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $locale = $request->getAttribute('locale');
        $path = $request->getUri()->getPath();
        $body = 'locale is ' . $locale . ' and path is ' . $path;

        return new HtmlResponse($body);
    }

}