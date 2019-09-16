<?php

namespace Bone\Traits;

trait LayoutAwareTrait
{
    /** @var string $layout */
    private $layout;

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }
}
