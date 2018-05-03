<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin\ApiQuoteRetrieval;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\QuoteLoader;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Grabs the Quote ID from GuestPaymentInformationManagement before it runs and gives it to the QuoteProvider
 *
 * @see GuestPaymentInformationManagementInterface
 * @see QuoteProviderInterface
 */
class GuestPaymentInformationManagementPlugin
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
     * @param  GuestPaymentInformationManagementInterface $subject
     * @param  int                                        $quoteId
     * @param  string                                     $email
     * @param  PaymentInterface                           $paymentMethod
     * @param  AddressInterface|null                      $billingAddress
     * @return void
     * @see    GuestPaymentInformationManagementInterface::savePaymentInformation()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
        $quoteId,
        $email,
        $paymentMethod,
        $billingAddress = null
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  GuestPaymentInformationManagementInterface $subject
     * @param  int                                        $quoteId
     * @param  string                                     $email
     * @param  PaymentInterface                           $paymentMethod
     * @param  AddressInterface|null                      $billingAddress
     * @return void
     * @see    GuestPaymentInformationManagementInterface::savePaymentInformationAndPlaceOrder()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for interceptors.
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $quoteId,
        $email,
        $paymentMethod,
        $billingAddress = null
    ) {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }

    /**
     * Load the quote used by the service contract in a saved object space for use in tax calculation.
     *
     * @param  GuestPaymentInformationManagementInterface $subject
     * @param  int                                        $quoteId
     * @see    GuestPaymentInformationManagementInterface::getPaymentInformation()
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject required for interceptors
     */
    public function beforeGetPaymentInformation(GuestPaymentInformationManagementInterface $subject, $quoteId)
    {
        if ($this->config->isVertexActive()) {
            $quote = $this->quoteLoader->getGuestQuoteModelById($quoteId);

            if ($quote !== null) {
                $this->quoteProvider->setQuote($quote);
            }
        }
    }
}
