<?php

namespace Bone\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HalCollection implements MiddlewareInterface
{
    /** @var int $numPerPage */
    private $numPerPage;

    /**
     * HalCollection constructor.
     * @param int $numPerPage
     */
    public function __construct(int $numPerPage)
    {
        $this->numPerPage = $numPerPage;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = $this->numPerPage;
        $params['limit'] = $limit;
        $params['offset'] = ($page *  $limit) - $limit;
        $request = $request->withQueryParams($params);

        $response = $handler->handle($request);

        $uri = $request->getUri();
        $data = json_decode($response->getBody()->getContents(), true);
        $pageCount = (int) ceil($data['total'] / $limit);

        $hal = [
            '_links' => [
                'self' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath(),
                ],
                'first' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath(),
                ],
            ],
        ];

        if ($page !== 1) {
            $hal['_links']['prev'] = [
                'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . ($page - 1),
            ];
        }

        if ($page !== $pageCount) {
            $hal['_links']['next'] = [
                'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . ($page + 1),
            ];
        }

        $hal['_links']['last'] = [
            'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . $pageCount,
        ];

        /** @todo add _self links for entities in collection */

        $data = array_merge($hal, $data);
        foreach ($data['_embedded'] as $key => $value) {
            $data['_embedded'][$key]['_links'] = [
                'self' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '/' . $value['id'],
                ],
            ];
        }
        $body = $response->getBody();
        $body->rewind();
        $body->write(json_encode($data));
        $response = $response->withHeader('Content-Type', 'application/hal+json');

        return $response->withBody($body);
    }
}