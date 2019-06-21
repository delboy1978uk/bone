<?php
/**
 * User: delboy1978uk
 * Date: 22/01/2017
 * Time: 17:28
 */

namespace Bone\Mvc\View;


interface ViewEngine
{
    /**
     * ViewEngine constructor.
     * @param $viewPath
     * @param null $extension
     */
    public function __construct($viewPath, $extension = null);

    /**
     * @param $view
     * @param $vars
     * @return mixed
     */
    public function render($view, array $vars = []);

    /**
     * Add a new template folder for grouping templates under different namespaces.
     * @param  string  $name
     * @param  string  $directory
     * @param  boolean $fallback
     * @return self
     */
    public function addFolder($name, $directory, $fallback = false);
}