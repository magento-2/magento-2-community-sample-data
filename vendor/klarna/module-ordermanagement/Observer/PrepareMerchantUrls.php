<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url;

/**
 * Class PrepareMerchantUrls
 *
 * @package Klarna\Ordermanagement\Observer
 */
class PrepareMerchantUrls implements ObserverInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * PrepareMerchantUrls constructor.
     *
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $urls = $observer->getUrls();
        if ($urls->hasPushUri()) { // Ignore for Kred
            return;
        }
        $urlParams = $observer->getUrlParams()->toArray();
        $urls->setPush($this->url->getDirectUrl('klarna/api/push/id/{checkout.order.id}', $urlParams));
        $urls->setNotification($this->url->getDirectUrl('klarna/api/notification/id/{checkout.order.id}', $urlParams));
    }
}
