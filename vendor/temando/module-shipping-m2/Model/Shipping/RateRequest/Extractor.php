<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipping\RateRequest;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Temando Rate Request Utility.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Extractor
{
    /**
     * Normalize rate request items. In rare cases they are not set at all.
     *
     * @param RateRequest $rateRequest
     * @return \Magento\Quote\Model\Quote\Item\AbstractItem[]
     */
    public function getItems(RateRequest $rateRequest)
    {
        if (!$rateRequest->getAllItems()) {
            return [];
        }

        return $rateRequest->getAllItems();
    }

    /**
     * Extract quote from rate request.
     *
     * @param RateRequest $rateRequest
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws LocalizedException
     */
    public function getQuote(RateRequest $rateRequest)
    {
        /** @var \Magento\Quote\Model\Quote\Item\AbstractItem[] $itemsToShip */
        $itemsToShip = $this->getItems($rateRequest);
        $currentItem = current($itemsToShip);

        if ($currentItem === false) {
            throw new LocalizedException(__('No items to ship found in rates request.'));
        }

        return $currentItem->getQuote();
    }

    /**
     * Extract shipping address from rate request.
     *
     * @param RateRequest $rateRequest
     * @return \Magento\Quote\Model\Quote\Address
     * @throws LocalizedException
     */
    public function getShippingAddress(RateRequest $rateRequest)
    {
        $itemsToShip = $this->getItems($rateRequest);
        $currentItem = current($itemsToShip);

        if ($currentItem === false) {
            throw new LocalizedException(__('No items to ship found in rates request.'));
        }

        return $currentItem->getAddress();
    }

    /**
     * Extract billing address from rate request.
     *
     * @param RateRequest $rateRequest
     * @return \Magento\Quote\Model\Quote\Address
     * @throws LocalizedException
     */
    public function getBillingAddress(RateRequest $rateRequest)
    {
        $quote = $this->getQuote($rateRequest);
        if (!$quote->getBillingAddress()->getCountryId()) {
            // billing address not selected yet, temporarily use shipping address.
            return $this->getShippingAddress($rateRequest);
        }

        return $quote->getBillingAddress();
    }
}
