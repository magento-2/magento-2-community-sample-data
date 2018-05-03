<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\GuestShippingInformationManagementInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Grabs the Quote ID from GuestShippingInformationManagement before it runs and gives it to the QuoteProvider
 *
 * @see GuestShippingInformationManagementInterface
 * @see QuoteProviderInterface
 */
class GuestShippingInformationManagementPlugin
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
     * @param  GuestShippingInformationManagementInterface $subject
     * @param  int                                         $quoteId
     * @param  ShippingInformationInterface                $addressInformation
     * @see    GuestShippingInformationManagementInterface::saveAddressInformation()
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSaveAddressInformation(
        GuestShippingInformationManagementInterface $subject,
        $quoteId,
        $addressInformation
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
