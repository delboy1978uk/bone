<?php

use Bone\Regex;

class BoneRegexTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // test object can be created 
    public function testCanConstructRegexObject()
    {
    	$regex = new Regex('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)');
        $this->assertTrue($this->regex);
    }



    // test getter and setter works
    public function testGetPattern()
    {
    	$regex = new Regex('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)');
        $this->assertEquals('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)',$regex->getPattern());
    }



    // test getter and setter works
    public function testSetPattern()
    {
    	$regex = new Regex('we will replace this non-regex nonsense');
    	$regex->setPattern('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)');
        $this->assertEquals('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)',$regex->getPattern());
    }


    // test the regex pattern matcher works
    public function testGetMatches()
    {
    	$regex = new Regex('^\/(?<controller>[^\/]+)\/(?<action>[^\/]+)\/(?<varvalpairs>(?:[^\/]+\/[^\/]+\/?)*)');
        $this->assertTrue($regex->getMatches('/controller/action/param/value/next-param/next-value'));
    }
}