<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\PackageInterfaceFactory;
use Temando\Shipping\Model\Shipment\PackageItemInterface;
use Temando\Shipping\Model\Shipment\PackageItemInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com
 */
class PackageResponseMapper
{
    /**
     * @var PackageInterfaceFactory
     */
    private $packageFactory;

    /**
     * @var PackageItemInterfaceFactory
     */
    private $packageItemFactory;

    /**
     * PackageResponseMapper constructor.
     * @param PackageInterfaceFactory $packageFactory
     * @param PackageItemInterfaceFactory $packageItemFactory
     */
    public function __construct(
        PackageInterfaceFactory $packageFactory,
        PackageItemInterfaceFactory $packageItemFactory
    ) {
        $this->packageFactory = $packageFactory;
        $this->packageItemFactory = $packageItemFactory;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Shipment\Attributes\Package\Item[] $apiPackageItems
     * @return PackageItemInterface[]
     */
    public function mapItems(array $apiPackageItems)
    {
        $packageItems = [];

        foreach ($apiPackageItems as $apiPackageItem) {
            /** @var \Temando\Shipping\Model\Shipment\PackageItem $packageItem */
            $packageItem = $this->packageItemFactory->create(['data' => [
                PackageItemInterface::DESCRIPTION => $apiPackageItem->getProduct()->getDescription(),
                PackageItemInterface::QTY => $apiPackageItem->getQuantity()
            ]]);
            $packageItems[]= $packageItem;
        }

        return $packageItems;
    }

    /**
     * @param Package $apiPackage
     * @return PackageInterface
     */
    public function map(Package $apiPackage)
    {
        $dimensions      = $apiPackage->getDimensions();
        $grossWeight     = $apiPackage->getGrossWeight();

        $items = $this->mapItems($apiPackage->getItems());

        /** @var \Temando\Shipping\Model\Shipment\Package $package */
        $package = $this->packageFactory->create(['data' => [
            PackageInterface::PACKAGE_ID => $apiPackage->getId(),
            PackageInterface::TRACKING_REFERENCE => $apiPackage->getTrackingReference(),
            PackageInterface::WEIGHT => sprintf(
                '%s %s',
                $grossWeight->getValue(),
                $grossWeight->getUnitOfMeasurement()
            ),
            PackageInterface::WIDTH => sprintf('%s %s', $dimensions->getWidth(), $dimensions->getUnit()),
            PackageInterface::LENGTH => sprintf('%s %s', $dimensions->getLength(), $dimensions->getUnit()),
            PackageInterface::HEIGHT => sprintf('%s %s', $dimensions->getHeight(), $dimensions->getUnit()),
            PackageInterface::ITEMS => $items
        ]]);

        return $package;
    }
}
