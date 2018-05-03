<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Quote\Api\GuestShipmentEstimationInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Grabs the Quote ID from GuestShipmentEstimation before it runs and gives it to the QuoteProvider
 *
 * @see GuestShipmentEstimationInterface
 * @see QuoteProviderInterface
 */
class GuestShipmentEstimationPlugin
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
     * @param  GuestShipmentEstimationInterface $subject
     * @param  int                              $cartId
     * @param  AddressInterface                 $address
     * @see    GuestShipmentEstimationInterface::estimateByExtendedAddress()
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeEstimateByExtendedAddress(
        GuestShipmentEstimationInterface $subject,
        $cartId,
        AddressInterface $address
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
