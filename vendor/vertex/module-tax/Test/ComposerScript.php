<?php

namespace Vertex\Tax\Test;

use Magento\FunctionalTestingFramework\Util\TestGenerator;

class ComposerScript
{
    public static function generateFunctionalTests()
    {
        require_once __DIR__.'/Functional/functional/_bootstrap.php';
        TestGenerator::getInstance()->createAllCestFiles();
    }
}
