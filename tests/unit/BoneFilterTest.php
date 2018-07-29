<?php

use Bone\Filter;
use Bone\Filter\FilterException;

class BoneFilterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCanFilterString()
    {
        $this->assertEquals('boneMVC', Filter::filterString('bone-m-v-c', 'DashToCamelCase'));
    }

    public function testThrowsException()
    {
        $this->expectException(FilterException::class);
        Filter::filterString('test string', 'NonExistentFilter');
    }

    public function testWithFullyNamespacedClass()
    {
        $string = Filter::filterString('bone-m-v-c', '\Bone\Filter\String\DashToCamelCase');
        $this->assertEquals('boneMVC', $string);
    }
}