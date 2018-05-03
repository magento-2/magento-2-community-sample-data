<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Quote\Api\GuestCartTotalManagementInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\TotalsAdditionalDataInterface;

/**
 * Grabs the Quote ID from class GuestCartTotalManagement before it runs and gives it to the QuoteProvider.
 *
 * @see GuestCartTotalManagementInterface
 * @see QuoteProviderInterface
 */
class GuestCartTotalManagementPlugin
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
     * @param  GuestCartTotalManagementInterface $subject
     * @param  int                               $cartId
     * @param  PaymentInterface                  $paymentMethod
     * @param  string                            $shippingCarrierCode
     * @param  string                            $shippingMethodCode
     * @param  TotalsAdditionalDataInterface     $additionalData
     * @return void
     * @see    GuestCartTotalManagementInterface::collectTotals()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeCollectTotals(
        GuestCartTotalManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        $shippingCarrierCode = null,
        $shippingMethodCode = null,
        TotalsAdditionalDataInterface $additionalData = null
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
