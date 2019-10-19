<?php

namespace Bone\Mvc\Controller;

use Bone\Mvc\View\PlatesEngine;

interface ViewAwareInterface
{
    public function setView(PlatesEngine $view): void;
    public function getView(): PlatesEngine;
}