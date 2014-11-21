<?php
/**
 * bone
 * delboy1978uk
 * 29/06/2014
 */

namespace Bone\Mvc\Response;

class Headers
{
    /** @var array */
    private $headers;

    public function __construct()
    {
        $this->headers = array();
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key,$value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getHeader($key)
    {
        if(array_key_exists($key,$this->headers))
        {
            return $this->headers[$key];
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return $this->headers;
    }

    /**
     *  Fire th' cannons!
     */
    public function dispatch()
    {
        foreach($this->headers as $key => $val)
        {
            header($key.': '.$val);
        }
        return true;
    }

    /**
     *  we be wantin' t' see Json
     */
    public function setJsonResponse()
    {
        $this->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $this->setHeader('Expires','Mon, 26 Jul 1997 05:00:00 GMT');
        $this->setHeader('Content-Type','application/json');
    }
}