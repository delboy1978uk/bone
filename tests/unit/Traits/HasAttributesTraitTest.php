<?php

use Bone\Server\Environment;
use Codeception\TestCase\Test;

class HasAttributesTraitTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSetAttributes()
    {
        $server = new Environment([]);
        $server->setAttributes([
            'test' => 'test1',
            'hello' => 'world',
        ]);
        $this->assertCount(2, $server->getAttributes());
        $this->assertEquals('test1', $server->getAttribute('test'));
        $this->assertEquals('world', $server->getAttribute('hello'));
        $server->setAttribute('another', 'value');
        $this->assertCount(3, $server->getAttributes());
        $this->assertEquals('value', $server->getAttribute('another'));
    }
}