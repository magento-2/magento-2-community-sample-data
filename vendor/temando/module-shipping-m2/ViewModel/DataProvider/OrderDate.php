<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Text;
use Magento\Store\Model\ScopeInterface;

/**
 * Order Info Date Formatter
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderDate implements OrderDateInterface
{
    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * @var Text
     */
    private $block;

    /**
     * OrderDate constructor.
     * @param TimezoneInterface $date
     * @param Text $block
     */
    public function __construct(TimezoneInterface $date, Text $block)
    {
        $this->date = $date;
        $this->block = $block;
    }

    /**
     * Create DateTime object with given date in the current locale.
     *
     * @param string $date
     * @return \DateTime
     */
    public function getDate(string $date): \DateTime
    {
        $localizedDate = $this->date->date(new \DateTime($date));
        return $localizedDate;
    }

    /**
     * Format given date in the locale configured for given store.
     *
     * @param string $date
     * @param int $storeId
     * @return string
     */
    public function getStoreDate(string $date, int $storeId): string
    {
        if (empty($date)) {
            return '';
        }

        $timezone = $this->date->getConfigTimezone(ScopeInterface::SCOPE_STORE, $storeId);
        $storeDate = $this->block->formatDate(
            $date,
            \IntlDateFormatter::MEDIUM,
            true,
            $timezone
        );

        return $storeDate;
    }

    /**
     * Format given date in the current locale.
     *
     * @param string $date
     * @return string
     */
    public function getAdminDate(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $localizedDate = $this->getDate($date);
        $adminDate = $this->block->formatDate(
            $localizedDate,
            \IntlDateFormatter::MEDIUM,
            true
        );

        return $adminDate;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getStoreTimezone(int $storeId): string
    {
        return $this->date->getConfigTimezone(ScopeInterface::SCOPE_STORE, $storeId);
    }
}
