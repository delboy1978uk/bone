<?php

namespace BoneTest\TestPackage\Command;

use Symfony\Component\Console\Command\Command;

class TestCommand extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->setName('test');
    }
}