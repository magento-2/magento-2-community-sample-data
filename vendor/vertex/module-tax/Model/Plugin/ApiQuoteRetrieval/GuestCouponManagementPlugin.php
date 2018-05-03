<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Quote\Api\GuestCouponManagementInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Grabs the Quote ID from class GuestCouponManagement before it runs and gives it to the QuoteProvider.
 *
 * @see GuestCouponManagementInterface
 * @see QuoteProviderInterface
 */
class GuestCouponManagementPlugin
{
    /** @var Config */
    private $config;

    /** @var QuoteProviderInterface */
    private $quoteProvider;

    /** @var QuoteLoader */
    private $quoteLoader;

    /**
     * @param QuoteProviderInterface $quoteProvider
     * @param QuoteLoader            $quoteLoader
     * @param Config                 $config
     */
    public function __construct(QuoteProviderInterface $quoteProvider, QuoteLoader $quoteLoader, Config $config)
    {
        $this->quoteProvider = $quoteProvider;
        $this->quoteLoader = $quoteLoader;
        $this->config = $config;
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  GuestCouponManagementInterface $subject
     * @param  int                            $cartId
     * @return void
     * @see    GuestCouponManagementInterface::get()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject required for interceptors
     */
    public function beforeGet(GuestCouponManagementInterface $subject, $cartId)
    {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  GuestCouponManagementInterface $subject
     * @param  int                            $cartId
     * @param  string                         $couponCode
     * @return void
     * @see    GuestCouponManagementInterface::set()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSet(GuestCouponManagementInterface $subject, $cartId, $couponCode)
    {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  GuestCouponManagementInterface $subject
     * @param  int                            $cartId.
     * @return void
     * @see    GuestCouponManagementInterface::remove()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject required for interceptors
     */
    public function beforeRemove(GuestCouponManagementInterface $subject, $cartId)
    {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
