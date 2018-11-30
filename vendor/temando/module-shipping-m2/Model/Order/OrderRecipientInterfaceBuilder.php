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
 * Temando Order Recipient Builder
 *
 * Create a recipient entity to be shared between shipping module and Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderRecipientInterfaceBuilder extends AbstractSimpleObjectBuilder
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
            $shippingAddress = $this->rateRequestExtractor->getShippingAddress($rateRequest);

            $this->_set(OrderRecipientInterface::COMPANY, $shippingAddress->getCompany());
            $this->_set(OrderRecipientInterface::LASTNAME, $shippingAddress->getLastname());
            $this->_set(OrderRecipientInterface::FIRSTNAME, $shippingAddress->getFirstname());
            $this->_set(OrderRecipientInterface::EMAIL, $shippingAddress->getEmail());
            $this->_set(OrderRecipientInterface::PHONE, $shippingAddress->getTelephone());
            $this->_set(OrderRecipientInterface::FAX, $shippingAddress->getFax());
        } catch (LocalizedException $e) {
            // detailed address data unavailable
            $this->_set(OrderRecipientInterface::COMPANY, '');
            $this->_set(OrderRecipientInterface::LASTNAME, '');
            $this->_set(OrderRecipientInterface::FIRSTNAME, '');
            $this->_set(OrderRecipientInterface::EMAIL, '');
            $this->_set(OrderRecipientInterface::PHONE, '');
            $this->_set(OrderRecipientInterface::FAX, '');
        }

        if (is_array($rateRequest->getDestStreet())) {
            $destStreet = $rateRequest->getDestStreet();
        } else {
            $destStreet = explode("\n", $rateRequest->getDestStreet());
        }

        $this->_set(OrderRecipientInterface::NATIONAL_ID, '');
        $this->_set(OrderRecipientInterface::TAX_ID, '');
        $this->_set(OrderRecipientInterface::STREET, $destStreet);
        $this->_set(OrderRecipientInterface::COUNTRY_CODE, $rateRequest->getDestCountryId());
        $this->_set(OrderRecipientInterface::REGION, $rateRequest->getDestRegionCode());
        $this->_set(OrderRecipientInterface::POSTAL_CODE, $rateRequest->getDestPostcode());
        $this->_set(OrderRecipientInterface::CITY, $rateRequest->getDestCity());
        $this->_set(OrderRecipientInterface::SUBURB, '');
        $this->_set(OrderRecipientInterface::LONGITUDE, null);
        $this->_set(OrderRecipientInterface::LATITUDE, null);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @return void
     * @throws LocalizedException
     */
    public function setOrder(OrderInterface $order)
    {
        $shippingAddress = $order->getShippingAddress();

        $this->_set(OrderRecipientInterface::COMPANY, $shippingAddress->getCompany());
        $this->_set(OrderRecipientInterface::LASTNAME, $shippingAddress->getLastname());
        $this->_set(OrderRecipientInterface::FIRSTNAME, $shippingAddress->getFirstname());
        $this->_set(OrderRecipientInterface::EMAIL, $shippingAddress->getEmail());
        $this->_set(OrderRecipientInterface::PHONE, $shippingAddress->getTelephone());
        $this->_set(OrderRecipientInterface::FAX, $shippingAddress->getFax());
        $this->_set(OrderRecipientInterface::NATIONAL_ID, '');
        $this->_set(OrderRecipientInterface::TAX_ID, '');
        $this->_set(OrderRecipientInterface::STREET, $shippingAddress->getStreet());
        $this->_set(OrderRecipientInterface::COUNTRY_CODE, $shippingAddress->getCountryId());
        $this->_set(OrderRecipientInterface::REGION, $shippingAddress->getRegionCode());
        $this->_set(OrderRecipientInterface::POSTAL_CODE, $shippingAddress->getPostcode());
        $this->_set(OrderRecipientInterface::CITY, $shippingAddress->getCity());
        $this->_set(OrderRecipientInterface::SUBURB, '');
        $this->_set(OrderRecipientInterface::LONGITUDE, '');
        $this->_set(OrderRecipientInterface::LATITUDE, '');
    }
}
