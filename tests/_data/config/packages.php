<?php

use BoneTest\TestPackage\TestPackagePackage;

return [
    'packages' => [
        TestPackagePackage::class,
    ],
    'viewFolder' => 'tests/_data/src/View', // deprecated? refactor? remove? add to interface?
];
