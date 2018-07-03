<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Documentation Collection Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DocumentationModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $documentationId = '1234';
        $description = 'Package Label';
        $type = 'packageLabels';
        $size = 'A6';
        $mimeType = 'image/png';
        $url = 'https://example.com/documents/label-1234';

        /** @var Documentation $documentation */
        $documentation = Bootstrap::getObjectManager()->create(Documentation::class, ['data' => [
            DocumentationInterface::DOCUMENTATION_ID => $documentationId,
            DocumentationInterface::NAME => $description,
            DocumentationInterface::TYPE => $type,
            DocumentationInterface::SIZE => $size,
            DocumentationInterface::MIME_TYPE => $mimeType,
            DocumentationInterface::URL => $url,
        ]]);

        $this->assertEquals($documentationId, $documentation->getDocumentationId());
        $this->assertEquals($description, $documentation->getName());
        $this->assertEquals($type, $documentation->getType());
        $this->assertEquals($size, $documentation->getSize());
        $this->assertEquals($mimeType, $documentation->getMimeType());
        $this->assertEquals($url, $documentation->getUrl());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $documentationId = '1234';
        $description = 'Package Label';
        $type = 'packageLabels';
        $size = 'A6';
        $mimeType = 'image/png';
        $url = 'https://example.com/documents/label-1234';

        /** @var Documentation $documentation */
        $documentation = Bootstrap::getObjectManager()->create(Documentation::class);

        $this->assertEmpty($documentation->getDocumentationId());

        $documentation->setData(Documentation::DOCUMENTATION_ID, $documentationId);
        $this->assertEquals($documentationId, $documentation->getDocumentationId());

        $documentation->setData(Documentation::NAME, $description);
        $this->assertEquals($description, $documentation->getName());

        $documentation->setData(Documentation::TYPE, $type);
        $this->assertEquals($type, $documentation->getType());

        $documentation->setData(Documentation::SIZE, $size);
        $this->assertEquals($size, $documentation->getSize());

        $documentation->setData(Documentation::MIME_TYPE, $mimeType);
        $this->assertEquals($mimeType, $documentation->getMimeType());

        $documentation->setData(Documentation::URL, $url);
        $this->assertEquals($url, $documentation->getUrl());
    }
}
