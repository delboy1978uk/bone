<?php

namespace Bone\Mvc\Controller;

use League\Route\Http\Exception\NotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class DownloadController
{
    /** @var string $uploadsDirectory */
    private $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        if (!is_dir($uploadsDirectory)) {
            throw new InvalidArgumentException('Directory not found');
        }

        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     */
    public function downloadAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $file = $request->getQueryParams()['file'];

        if (file_exists('public' . $file)) {
            $path = 'public' . $file;
        } else if (!file_exists($this->uploadsDirectory . $file)) {
            throw new Exception($path . ' not found.', 404);
        } else {
            $path = $this->uploadsDirectory . $file;
        }
        
        // magic_mime module installed?
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($path);
        } else if (function_exists('finfo_file')) { // or fileinfo module installed?
            $finfo = finfo_open(FILEINFO_MIME); // return mime type
            $mimeType = finfo_file($finfo, $path);
            finfo_close($finfo);
        }

        $contents = file_get_contents($path);
        $stream = new Stream('php://memory', 'r+');
        $stream->write($contents);
        $response = new Response();
        $response = $response->withBody($stream);
        $response = $response->withHeader('Content-Type', $mimeType);

        return $response;
    }
}