<?php

namespace Bone\Traits;

trait HasAttributesTrait
{
    /** @var array $attributes */
    private $attributes = [];

    /**
     * @param $key
     * @return mixed|null
     */
    public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?: $default;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
