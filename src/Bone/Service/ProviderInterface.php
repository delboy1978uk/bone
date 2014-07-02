<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Derek.mclean
 * Date: 02/07/14
 * Time: 14:33
 * To change this template use File | Settings | File Templates.
 */

namespace Bone\Service;

use Bone\Container;


interface ProviderInterface
{
    public function register(Container $container);
}