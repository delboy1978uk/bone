<?php

namespace Bone\View;

use Bone\View\PlatesEngine;

interface ViewAwareInterface
{
    /**
     * @param PlatesEngine $view
     */
    public function setView(PlatesEngine $view): void;

    /**
     * @return PlatesEngine
     */
    public function getView(): PlatesEngine;
}