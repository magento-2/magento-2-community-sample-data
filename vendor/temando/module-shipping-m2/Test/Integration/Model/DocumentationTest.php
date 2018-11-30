<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

class DocumentationTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager $objectManager */
    private $objectManager;
    /** @var Documentation $documentation*/
    private $documentation;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->documentation= $this->objectManager->create(Documentation::class);
        $this->documentation->setData(DocumentationInterface::NAME, 'NAME');
        $this->documentation->setData(DocumentationInterface::DOCUMENTATION_ID, 'DOCUMENTATION_ID');
        $this->documentation->setData(DocumentationInterface::MIME_TYPE, 'MIME_TYPE');
        $this->documentation->setData(DocumentationInterface::SIZE, 'SIZE');
        $this->documentation->setData(DocumentationInterface::TYPE, 'TYPE');
        $this->documentation->setData(DocumentationInterface::URL, 'URL');
    }

    /**
     * @test
     */
    public function getNameTest()
    {
        $result = $this->documentation->getName();
        $this->assertEquals($result, "NAME");
    }

    /**
     * @test
     */
    public function getDocumentationIdTest()
    {
        $result = $this->documentation->getDocumentationId();
        $this->assertEquals($result, "DOCUMENTATION_ID");
    }

    /**
     * @test
     */
    public function getMimeTypeTest()
    {
        $result = $this->documentation->getMimeType();
        $this->assertEquals($result, "MIME_TYPE");
    }

    /**
     * @test
     */
    public function getSizeTest()
    {
        $result = $this->documentation->getSize();
        $this->assertEquals($result, "SIZE");
    }

    /**
     * @test
     */
    public function getTypeTest()
    {
        $result = $this->documentation->getType();
        $this->assertEquals($result, "TYPE");
    }

    /**
     * @test
     */
    public function getUrlTest()
    {
        $result = $this->documentation->getUrl();
        $this->assertEquals($result, "URL");
    }
}
