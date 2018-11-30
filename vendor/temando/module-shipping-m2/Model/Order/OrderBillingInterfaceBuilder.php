<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\Api\AbstractSimpleObjectBuilder;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Api\Data\OrderInterface;
use Temando\Shipping\Model\Shipping\RateRequest\Extractor;

/**
 * Temando Order Billing Builder
 *
 * Create a billing address entity to be shared between shipping module and Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderBillingInterfaceBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @var Extractor
     */
    private $rateRequestExtractor;

    /**
     * @param ObjectFactory $objectFactory
     * @param Extractor $rateRequestExtractor
     */
    public function __construct(
        ObjectFactory $objectFactory,
        Extractor $rateRequestExtractor
    ) {
        $this->rateRequestExtractor = $rateRequestExtractor;

        parent::__construct($objectFactory);
    }

    /**
     * @param RateRequest $rateRequest
     * @return void
     */
    public function setRateRequest(RateRequest $rateRequest)
    {
        try {
            $billingAddress = $this->rateRequestExtractor->getBillingAddress($rateRequest);

            $this->_set(OrderRecipientInterface::COMPANY, $billingAddress->getCompany());
            $this->_set(OrderRecipientInterface::LASTNAME, $billingAddress->getLastname());
            $this->_set(OrderRecipientInterface::FIRSTNAME, $billingAddress->getFirstname());
            $this->_set(OrderRecipientInterface::EMAIL, $billingAddress->getEmail());
            $this->_set(OrderRecipientInterface::PHONE, $billingAddress->getTelephone());
            $this->_set(OrderRecipientInterface::FAX, $billingAddress->getFax());

            if (is_array($billingAddress->getStreet())) {
                $billingStreet = $billingAddress->getStreet();
            } else {
                $billingStreet = explode("\n", $billingAddress->getStreet());
            }

            $this->_set(OrderRecipientInterface::NATIONAL_ID, '');
            $this->_set(OrderRecipientInterface::TAX_ID, $billingAddress->getVatId());
            $this->_set(OrderRecipientInterface::STREET, $billingStreet);
            $this->_set(OrderRecipientInterface::COUNTRY_CODE, $billingAddress->getCountryId());
            $this->_set(OrderRecipientInterface::REGION, $billingAddress->getRegion());
            $this->_set(OrderRecipientInterface::POSTAL_CODE, $billingAddress->getPostcode());
            $this->_set(OrderRecipientInterface::CITY, $billingAddress->getCity());
            $this->_set(OrderRecipientInterface::SUBURB, '');
            $this->_set(OrderRecipientInterface::LONGITUDE, null);
            $this->_set(OrderRecipientInterface::LATITUDE, null);
        } catch (LocalizedException $e) {
            // detailed address data unavailable
            $this->_set(OrderRecipientInterface::COMPANY, '');
            $this->_set(OrderRecipientInterface::LASTNAME, '');
            $this->_set(OrderRecipientInterface::FIRSTNAME, '');
            $this->_set(OrderRecipientInterface::EMAIL, '');
            $this->_set(OrderRecipientInterface::PHONE, '');
            $this->_set(OrderRecipientInterface::FAX, '');
            $this->_set(OrderRecipientInterface::NATIONAL_ID, '');
            $this->_set(OrderRecipientInterface::TAX_ID, '');
            $this->_set(OrderRecipientInterface::STREET, []);
            $this->_set(OrderRecipientInterface::COUNTRY_CODE, '');
            $this->_set(OrderRecipientInterface::REGION, '');
            $this->_set(OrderRecipientInterface::POSTAL_CODE, '');
            $this->_set(OrderRecipientInterface::CITY, '');
            $this->_set(OrderRecipientInterface::SUBURB, '');
            $this->_set(OrderRecipientInterface::LONGITUDE, null);
            $this->_set(OrderRecipientInterface::LATITUDE, null);
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @return void
     * @throws LocalizedException
     */
    public function setOrder(OrderInterface $order)
    {
        $billingAddress = $order->getBillingAddress();

        $this->_set(OrderRecipientInterface::COMPANY, $billingAddress->getCompany());
        $this->_set(OrderRecipientInterface::LASTNAME, $billingAddress->getLastname());
        $this->_set(OrderRecipientInterface::FIRSTNAME, $billingAddress->getFirstname());
        $this->_set(OrderRecipientInterface::EMAIL, $billingAddress->getEmail());
        $this->_set(OrderRecipientInterface::PHONE, $billingAddress->getTelephone());
        $this->_set(OrderRecipientInterface::FAX, $billingAddress->getFax());
        $this->_set(OrderRecipientInterface::NATIONAL_ID, '');
        $this->_set(OrderRecipientInterface::TAX_ID, $billingAddress->getVatId());
        $this->_set(OrderRecipientInterface::STREET, $billingAddress->getStreet());
        $this->_set(OrderRecipientInterface::COUNTRY_CODE, $billingAddress->getCountryId());
        $this->_set(OrderRecipientInterface::REGION, $billingAddress->getRegionCode());
        $this->_set(OrderRecipientInterface::POSTAL_CODE, $billingAddress->getPostcode());
        $this->_set(OrderRecipientInterface::CITY, $billingAddress->getCity());
        $this->_set(OrderRecipientInterface::SUBURB, '');
        $this->_set(OrderRecipientInterface::LONGITUDE, null);
        $this->_set(OrderRecipientInterface::LATITUDE, null);
    }
}
