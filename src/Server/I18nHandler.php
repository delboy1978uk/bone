<?php declare(strict_types=1);

namespace Bone\Server;

use League\Route\Http\Exception\NotFoundException;
use Locale;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class I18nHandler
{
    const REGEX_LOCALE = '#^/(?P<locale>[a-z]{2}[-_][a-zA-Z]{2})(?:/|$)#';

    /** @var array */
    private $supportedLocales;



    /**
     * InternationalisationMiddleware constructor.
     * @param  $helper
     * @param string|null $defaultLocale
     */
    public function __construct(array $supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function handleI18n(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if (! preg_match(self::REGEX_LOCALE, $path, $matches)) {
            $path = '/' . Locale::getDefault() . $path;
            throw new NotFoundException($path);
        }

        $locale = $matches['locale'];
        $locale = Locale::canonicalize($locale);
        Locale::setDefault($locale);
        $path = substr($path, strlen($locale) + 1);
        $uri = $uri->withPath($path);
        $request = $request->withUri($uri);

        return $request;
    }
}