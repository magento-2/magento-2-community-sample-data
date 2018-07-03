<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Packaging Model Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PackagingModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function dataIsSetThroughConstructorArgument()
    {
        $packagingId = '5ad6c7fd-ebb0-4b75-9d73-6b762ec41908';
        $name = 'C4 Letter';
        $type = 'Letter';
        $width = '224 mm';
        $length = '319 mm';
        $height = '3 mm';
        $tareWeight = '25 g';
        $maxWeight = '1 kg';

        /** @var Packaging $packaging */
        $packaging = Bootstrap::getObjectManager()->create(Packaging::class, ['data' => [
            Packaging::PACKAGING_ID => $packagingId,
            Packaging::NAME => $name,
            Packaging::TYPE => $type,
            Packaging::WIDTH => $width,
            Packaging::LENGTH => $length,
            Packaging::HEIGHT => $height,
            Packaging::TARE_WEIGHT => $tareWeight,
            Packaging::MAX_WEIGHT => $maxWeight,
        ]]);

        $this->assertEquals($packagingId, $packaging->getPackagingId());
        $this->assertEquals($name, $packaging->getName());
        $this->assertEquals($type, $packaging->getType());
        $this->assertEquals($width, $packaging->getWidth());
        $this->assertEquals($length, $packaging->getLength());
        $this->assertEquals($height, $packaging->getHeight());
        $this->assertEquals($tareWeight, $packaging->getTareWeight());
        $this->assertEquals($maxWeight, $packaging->getMaxWeight());
    }

    /**
     * @test
     */
    public function dataIsSetThroughSetters()
    {
        $packagingId = '5ad6c7fd-ebb0-4b75-9d73-6b762ec41908';
        $name = 'C4 Letter';
        $type = 'Letter';
        $width = '224 mm';
        $length = '319 mm';
        $height = '3 mm';
        $tareWeight = '25 g';
        $maxWeight = '1 kg';

        /** @var Packaging $packaging */
        $packaging = Bootstrap::getObjectManager()->create(Packaging::class);

        $this->assertEmpty($packaging->getPackagingId());

        $packaging->setData(Packaging::PACKAGING_ID, $packagingId);
        $this->assertEquals($packagingId, $packaging->getPackagingId());

        $packaging->setData(Packaging::NAME, $name);
        $this->assertEquals($name, $packaging->getName());

        $packaging->setData(Packaging::TYPE, $type);
        $this->assertEquals($type, $packaging->getType());

        $packaging->setData(Packaging::WIDTH, $width);
        $this->assertEquals($width, $packaging->getWidth());

        $packaging->setData(Packaging::LENGTH, $length);
        $this->assertEquals($length, $packaging->getLength());

        $packaging->setData(Packaging::HEIGHT, $height);
        $this->assertEquals($height, $packaging->getHeight());

        $packaging->setData(Packaging::TARE_WEIGHT, $tareWeight);
        $this->assertEquals($tareWeight, $packaging->getTareWeight());

        $packaging->setData(Packaging::MAX_WEIGHT, $maxWeight);
        $this->assertEquals($maxWeight, $packaging->getMaxWeight());
    }
}
