<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Customer\Model\Address;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Temando Order Interface Builder Test
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderInterfaceBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return Address
     */
    private function getInstantPurchaseAddress()
    {
        $addressData = [
            'country_id' => 'GB',
            'region_id' => '0',
            'street' => '909 Foo Road',
            'city' => 'London',
            'postcode' => 'SE17 1LB',
        ];

        $address = Bootstrap::getObjectManager()->create(Address::class, ['data' => $addressData]);

        return $address;
    }

    /**
     * The Magento_InstantPurchase module passes a very "streamlined" rates
     * request to the shipping carriers. Make sure the order request builder
     * handles this well.
     *
     * @see \Magento\InstantPurchase\Model\ShippingMethodChoose\ShippingRateFinder::getRatesForCustomerAddress
     *
     * @magentoConfigFixture default_store general/store_information/name Foo Store
     * @test
     */
    public function buildInstantPurchaseOrderRequest()
    {
        $address = $this->getInstantPurchaseAddress();

        /** @var $rateRequest RateRequest */
        $rateRequest = Bootstrap::getObjectManager()->create(RateRequest::class);
        $rateRequest->setDestCountryId($address->getCountryId());
        $rateRequest->setDestRegionId($address->getRegionId());
        $rateRequest->setDestRegionCode($address->getRegionCode());
        $rateRequest->setDestStreet($address->getStreetFull());
        $rateRequest->setDestCity($address->getCity());
        $rateRequest->setDestPostcode($address->getPostcode());
        $rateRequest->setStoreId('1');
        $rateRequest->setWebsiteId('1');
        $rateRequest->setBaseCurrency('GBP');
        $rateRequest->setPackageCurrency('EUR');
        $rateRequest->setPackageQty(-1);

        /** @var OrderInterfaceBuilder $builder */
        $builder = Bootstrap::getObjectManager()->create(OrderInterfaceBuilder::class);
        $builder->setRateRequest($rateRequest);

        /** @var OrderInterface $orderType */
        $orderType = $builder->create();
        $this->assertSame($address->getCountryId(), $orderType->getRecipient()->getCountryCode());
        $this->assertSame($address->getCity(), $orderType->getRecipient()->getCity());
        $this->assertSame($address->getPostcode(), $orderType->getRecipient()->getPostalCode());
    }
}
