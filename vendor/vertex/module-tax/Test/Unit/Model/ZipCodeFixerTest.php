<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Model;

use Vertex\Tax\Model\ZipCodeFixer;
use Vertex\Tax\Test\Unit\TestCase;

class ZipCodeFixerTest extends TestCase
{
    public function dataProvider()
    {
        return [
            ['245', '00245'],
            ['2801-1234', '02801-1234'],
            ['44131', '44131'],
            ['44131-1234', '44131-1234'],
            ['02801-1234', '02801-1234'],
            [null, null],
        ];
    }

    /**
     * @param string $codeToFix
     * @param string $codeToExpect
     * @dataProvider dataProvider
     */
    public function testFix($codeToFix, $codeToExpect)
    {
        $fixer = new ZipCodeFixer();
        $this->assertEquals($codeToExpect, $fixer->fix($codeToFix));
    }
}
