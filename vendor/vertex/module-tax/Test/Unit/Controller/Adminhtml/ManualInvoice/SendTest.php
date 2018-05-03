<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Unit\Controller\Adminhtml\ManualInvoice;

use Vertex\Tax\Test\Unit\TestCase;
use Vertex\Tax\Controller\Adminhtml\ManualInvoice\Send;

class SendTest extends TestCase
{
    /**
     * @group featureManualInvoice
     * @covers \Vertex\Tax\Controller\Adminhtml\ManualInvoice\Send::__construct()
     */
    public function testEnsureNoErrorsDuringConstruction()
    {
        $this->getObject(Send::class);
    }
}
