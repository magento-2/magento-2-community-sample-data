<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\Shipment\PackageInterface;
use Temando\Shipping\Model\Shipment\PackageInterfaceFactory;
use Temando\Shipping\Model\Shipment\PackageItemInterface;
use Temando\Shipping\Model\Shipment\PackageItemInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Generic\Package;

/**
 * Map API data to application data object
 *
 * @package Temando\Shipping\Rest
 * @author  Rhodri Davies <rhodri.davies@temando.com>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com
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
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Item[] $apiPackageItems
     * @return PackageItemInterface[]
     */
    public function mapItems(array $apiPackageItems)
    {
        $packageItems = [];

        foreach ($apiPackageItems as $apiPackageItem) {
            $apiProduct = $apiPackageItem->getProduct();

            $weight = $apiProduct->getWeight();
            $monetaryValue = $apiProduct->getMonetaryValue();

            $packageItemData = [
                PackageItemInterface::SKU => $apiProduct->getSku(),
                PackageItemInterface::DESCRIPTION => $apiProduct->getDescription(),
                PackageItemInterface::QTY => $apiPackageItem->getQuantity(),
                PackageItemInterface::UNIT => $apiProduct->getUnit(),
            ];

            if ($classificationCodes = $apiProduct->getClassificationCodes()) {
                if ($hsCode = $classificationCodes->getHsCode()) {
                    $packageItemData[PackageItemInterface::HS_CODE] = $hsCode;
                }
            }

            if ($manufacture = $apiProduct->getManufacture()) {
                if ($manufactureAddress = $manufacture->getAddress()) {
                    if ($manufactureCountryCode = $manufactureAddress->getCountryCode()) {
                        $packageItemData[PackageItemInterface::COUNTRY_OF_MANUFACTURE] = $manufactureCountryCode;
                    }
                }
            }

            if ($origin = $apiProduct->getOrigin()) {
                if ($originAddress = $origin->getAddress()) {
                    if ($originCountryCode = $originAddress->getCountryCode()) {
                        $packageItemData[PackageItemInterface::COUNTRY_OF_ORIGIN] = $originCountryCode;
                    }
                }
            }

            if ($weight) {
                $packageItemData[PackageItemInterface::WEIGHT] = sprintf(
                    '%s %s',
                    $weight->getValue(),
                    $weight->getUnit()
                );
            }

            if ($monetaryValue) {
                $packageItemData[PackageItemInterface::MONETARY_VALUE] = sprintf(
                    '%s %s',
                    $monetaryValue->getAmount(),
                    $monetaryValue->getCurrency()
                );
            }

            $packageItemFactoryData = ['data' => $packageItemData];

            /** @var \Temando\Shipping\Model\Shipment\PackageItem $packageItem */
            $packageItem = $this->packageItemFactory->create($packageItemFactoryData);
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
        $dimensions = $apiPackage->getDimensions();
        $grossWeight = $apiPackage->getGrossWeight();

        $items = $this->mapItems($apiPackage->getItems());

        /** @var \Temando\Shipping\Model\Shipment\Package $package */
        $package = $this->packageFactory->create(['data' => [
            PackageInterface::PACKAGE_ID => $apiPackage->getId(),
            PackageInterface::TRACKING_REFERENCE => $apiPackage->getTrackingReference(),
            PackageInterface::WEIGHT => sprintf(
                '%s %s',
                $grossWeight->getValue(),
                $grossWeight->getUnit()
            ),
            PackageInterface::WIDTH => sprintf('%s %s', $dimensions->getWidth(), $dimensions->getUnit()),
            PackageInterface::LENGTH => sprintf('%s %s', $dimensions->getLength(), $dimensions->getUnit()),
            PackageInterface::HEIGHT => sprintf('%s %s', $dimensions->getHeight(), $dimensions->getUnit()),
            PackageInterface::ITEMS => $items
        ]]);

        return $package;
    }
}
