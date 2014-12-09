<?php
/**
 * User: delboy1978uk
 * Date: 09/12/2014
 * Time: 02:54
 */

namespace Bone;


/**
 * Class ObjectFactory
 * @package Bone
 */
abstract class ObjectFactory
{
    /**
     * @var array
     */
    protected $required = array();

    /**
     * @param $arg1
     * @param $arg2
     * @return mixed
     */
    abstract function create($arg1,$arg2);

    /**
     * @param $options
     * @return bool
     */
    public function validateOptions($options)
    {
        foreach($this->required as $key)
        {
            if(!array_key_exists($key,$options))
            {
                return false;
            }
        }
        return true;
    }
}