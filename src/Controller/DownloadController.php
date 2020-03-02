<?php

namespace Bone\Controller;

use Bone\Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;

class DownloadController
{
    /** @var string $uploadsDirectory */
    private $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        if (!is_dir($uploadsDirectory)) {
            throw new InvalidArgumentException('Directory ' . $uploadsDirectory . ' not found');
        }

        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws Exception
     */
    public function downloadAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $path = $this->getFilePath($queryParams);
        $mimeType = $this->getMimeType($path);
        $contents = file_get_contents($path);
        $stream = new Stream('php://memory', 'r+');
        $stream->write($contents);
        $response = new Response();
        $response = $response->withBody($stream);
        $response = $response->withHeader('Content-Type', $mimeType);

        return $response;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getMimeType(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME); // return mime type
        $mimeType = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mimeType;
    }

    /**
     * @param array $queryParams
     * @return string
     * @throws Exception
     */
    private function getFilePath(array $queryParams): string
    {
        if (!isset($queryParams['file'])) {
            throw new Exception('Invalid Request.', 400);
        }

        $file = $queryParams['file'];
        $path = $this->uploadsDirectory . $file;

        if (file_exists('public' . $file)) {
            $path = 'public' . $file;
        } else if (!file_exists($path)) {
            throw new Exception($path . ' not found.', 404);
        }

        return $path;
    }
}