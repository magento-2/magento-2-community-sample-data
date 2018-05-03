<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Grabs the Quote ID from PaymentInformationManagement before it runs and gives it to the QuoteProvider
 *
 * @see PaymentInformationManagementInterface
 * @see QuoteProviderInterface
 */
class PaymentInformationManagementPlugin
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
     * @param  PaymentInformationManagementInterface $subject
     * @param  int                                   $quoteId
     * @param  PaymentInterface                      $paymentMethod
     * @param  AddressInterface|null                 $billingAddress
     * @return void
     * @see    PaymentInformationManagementInterface::savePaymentInformation()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $quoteId,
        $paymentMethod,
        $billingAddress = null
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteProvider->getQuote();

            if ($quote !== null && $quote->getId() == $quoteId) {
                return;
            }

            $quote = $this->quoteLoader->getQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  PaymentInformationManagementInterface $subject
     * @param  int                                   $quoteId
     * @param  PaymentInterface                      $paymentMethod
     * @param  AddressInterface|null                 $billingAddress
     * @return void
     * @see    PaymentInformationManagementInterface::savePaymentInformationAndPlaceOrder()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $quoteId,
        $paymentMethod,
        $billingAddress = null
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteProvider->getQuote();

            if ($quote !== null && $quote->getId() == $quoteId) {
                return;
            }

            $quote = $this->quoteLoader->getQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  PaymentInformationManagementInterface $subject
     * @param  int                                   $quoteId
     * @return void
     * @see    PaymentInformationManagementInterface::getPaymentInformation()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject required for interceptors
     */
    public function beforeGetPaymentInformation(PaymentInformationManagementInterface $subject, $quoteId)
    {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteProvider->getQuote();

            if ($quote !== null && $quote->getId() == $quoteId) {
                return;
            }

            $quote = $this->quoteLoader->getQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
