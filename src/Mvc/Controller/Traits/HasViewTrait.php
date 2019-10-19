<?php

namespace Bone\Mvc\Controller\Traits;

use Bone\Mvc\Controller\ViewAwareInterface;
use Bone\Mvc\View\PlatesEngine;

trait HasViewTrait
{
    /** @var PlatesEngine $view */
    protected $view;

    /**
     * @param PlatesEngine $view
     */
    public function setView(PlatesEngine $view): void
    {
        $this->view = $view;
    }

    /**
     * @return PlatesEngine
     */
    public function getView(): PlatesEngine
    {
        return $this->view;
    }
}