<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Ui\Component\MassAction;

use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\Model\LocationInterface;
use Temando\Shipping\Model\PackagingInterface;
use Temando\Shipping\Model\ResourceModel\Repository\CarrierRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\LocationRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PackagingRepositoryInterface;

/**
 * Temando Mass Action ID Filter
 *
 * @package  Temando\Shipping\Ui
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Filter
{
    /**
     * Obtain the list of selected carrier configurations:
     * - inclusive:
     * -- select all on this page OR tick some: non-empty `$selected` array
     * - exclusive:
     * -- select all: empty `$selected` array, empty `$excluded` array
     * -- select all, then un-tick: empty `$selected` array, non-empty `$excluded` array
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param string[] $selected
     * @param string|string[] $excluded
     * @return string[]
     */
    public function getCarrierIds(CarrierRepositoryInterface $carrierRepository, array $selected, array $excluded)
    {
        if (!empty($selected)) {
            return $selected;
        }

        // read all ids from repo
        $carriers = $carrierRepository->getList();
        $selected = array_map(function (CarrierInterface $carrier) {
            return $carrier->getConfigurationId();
        }, $carriers);
        // remove $excluded from ids
        $selected = array_diff($selected, $excluded);

        return $selected;
    }

    /**
     * Obtain the list of selected locations:
     * - inclusive:
     * -- some items: non-empty `$selected` array
     * - exclusive:
     * -- all items: empty `$selected` array, empty `$excluded` array
     * -- some items: empty `$selected` array, non-empty `$excluded` array
     *
     * @param LocationRepositoryInterface $locationRepository
     * @param string[] $selected
     * @param string|string[] $excluded
     * @return string[]
     */
    public function getLocationIds(LocationRepositoryInterface $locationRepository, $selected, $excluded)
    {
        if (!empty($selected)) {
            return $selected;
        }

        // read all ids from repo
        $locations = $locationRepository->getList();
        $selected = array_map(function (LocationInterface $location) {
            return $location->getLocationId();
        }, $locations);
        // remove $excluded from ids
        $selected = array_diff($selected, $excluded);

        return $selected;
    }

    /**
     * Obtain the list of selected containers:
     * - inclusive:
     * -- some items: non-empty `$selected` array
     * - exclusive:
     * -- all items: empty `$selected` array, empty `$excluded` array
     * -- some items: empty `$selected` array, non-empty `$excluded` array
     *
     * @param PackagingRepositoryInterface $packagingRepository
     * @param string[] $selected
     * @param string|string[] $excluded
     * @return string[]
     * @throws LocalizedException
     */
    public function getPackagingIds(PackagingRepositoryInterface $packagingRepository, $selected, $excluded)
    {
        if (!empty($selected)) {
            return $selected;
        }

        // read all ids from repo
        $containers = $packagingRepository->getList();
        $selected = array_map(function (PackagingInterface $container) {
            return $container->getPackagingId();
        }, $containers);
        // remove $excluded from ids
        $selected = array_diff($selected, $excluded);

        return $selected;
    }
}
