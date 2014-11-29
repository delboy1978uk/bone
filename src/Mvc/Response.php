<?php

namespace Bone\Mvc;

use Bone\Mvc\Response\Headers;
use Bone\Filter;

class Response
{
    /** @var Headers */
    private $headers;

    /** @var mixed */
    private $body;


    /**
     * @return Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param Headers $headers
     * @return $this
     */
    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }


    /**
     *  Fire th' Cannons!!
     *
     * @return string
     */
    public function send()
    {
        $this->headers->dispatch();
        echo $this->body;
    }
}
