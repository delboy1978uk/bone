<?php


use Bone\Application;
use Bone\App\AppPackage;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\User\BoneUserPackage;
use BoneTest\TestPackage\TestPackagePackage;
use Codeception\TestCase\Test;
use Laminas\Diactoros\Response;

class FormTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testForm()
    {
        $translator = $this->getMockBuilder(\Laminas\I18n\Translator\Translator::class)->getMock();
        $form = new \Bone\I18n\Form('testform', $translator);
        $form->init();
        $this->assertInstanceOf(\Laminas\I18n\Translator\Translator::class, $form->getTranslator());
    }
}


