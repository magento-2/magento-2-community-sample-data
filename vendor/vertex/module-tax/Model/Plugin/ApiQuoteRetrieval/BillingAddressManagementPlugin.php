<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Quote\Api\BillingAddressManagementInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Grabs the Quote ID from class BillingAddressManagement before it runs and gives it to the QuoteProvider.
 *
 * @see BillingAddressManagementInterface
 * @see QuoteProviderInterface
 */
class BillingAddressManagementPlugin
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
     * @param  BillingAddressManagementInterface. $subject
     * @param  int                                $cartId
     * @param  AddressInterface                   $address
     * @param  bool                               $useForShipping
     * @return int Address ID.
     * @see    BillingAddressManagementInterface::assign()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeAssign(
        BillingAddressManagementInterface $subject,
        $cartId,
        AddressInterface $address,
        $useForShipping = false
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getQuoteModelById($cartId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
