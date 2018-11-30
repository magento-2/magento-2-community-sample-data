<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

/**
 * Order Info Date Formatter Interface
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface OrderDateInterface
{
    /**
     * @param string $date
     * @return \DateTime
     */
    public function getDate(string $date): \DateTime;

    /**
     * @param string $date
     * @param int $storeId
     * @return string
     */
    public function getStoreDate(string $date, int $storeId): string;

    /**
     * @param string $date
     * @return string
     */
    public function getAdminDate(string $date): string;

    /**
     * @param int $storeId
     * @return string
     */
    public function getStoreTimezone(int $storeId): string;
}
