<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Test\Integration\Provider;

use Magento\Directory\Model\Currency;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterface;
use Temando\Shipping\Webservice\Response\Type\OrderResponseTypeInterfaceFactory;

class RateRequestProvider
{
    /**
     * @return RateRequest[][]
     */
    public static function getRateRequest()
    {
        /** @var QuoteFactory $quoteFactory */
        $quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
        /** @var ItemFactory $quoteItemFactory */
        $quoteItemFactory = Bootstrap::getObjectManager()->get(ItemFactory::class);
        /** @var AddressFactory $quoteAddressFactory */
        $quoteAddressFactory = Bootstrap::getObjectManager()->get(AddressFactory::class);

        $updatedAt = '0000';
        $createdAt = '1999';
        $quoteId = 808;

        $addressCompany = 'Foo Customer';
        $addressLastname = 'Lastname';
        $addressFirstname = 'Foo';
        $addressEmail = 'foo@example.com';
        $addressTelephone = '1800';
        $addressFax = '';

        $addressCountryId = 'DE';
        $addressRegionId = '91';
        $addressRegionCode = 'SAS';
        $addressStreet = 'Nonnenstraße 11';
        $addressCity = 'Leipzig';
        $addressPostalCode = '04229';

        $address = $quoteAddressFactory->create(['data' => [
            'company' => $addressCompany,
            'lastname' => $addressLastname,
            'firstname' => $addressFirstname,
            'email' => $addressEmail,
            'telephone' => $addressTelephone,
            'fax' => $addressFax,
            'country_id' => $addressCountryId,
            'region_id' => $addressRegionId,
            'region_code' => $addressRegionCode,
            'street' => $addressStreet,
            'city' => $addressCity,
            'postcode' => $addressPostalCode,
        ]]);

        // replace by mock in test, address handling is hard to provide
        $quote = $quoteFactory->create(['data' => [
            'id' => $quoteId,
            'store_id' => 1,
            'updated_at' => $updatedAt,
            'created_at' => $createdAt,
            'items_qty' => 1,
            'shipping_address' => $address,
            'billing_address' => $address,
        ]]);

        $quoteItem = $quoteItemFactory->create(['data' => ['qty' => 1]]);
        $quoteItem->setQuote($quote);

        $allItems = [$quoteItem];
        $currency = Bootstrap::getObjectManager()->create(Currency::class, ['data' => ['currency_code' => 'EUR']]);
        $rateRequest = new RateRequest([
            'all_items' => $allItems,
            'dest_country_id' => $addressCountryId,
            'dest_region_id' => $addressRegionId,
            'dest_region_code' => $addressRegionCode,
            'dest_street' => $addressStreet,
            'dest_city' => $addressCity,
            'dest_postcode' => $addressPostalCode,
            'package_currency' => $currency,
            'store_id' => '1',
            'website_id' => '1',
        ]);

        return [
            'request_1' => [
                $rateRequest
            ]
        ];
    }

    /**
     * @return RateRequest|OrderResponseTypeInterface[][]
     */
    public static function getRateRequestWithShippingExperience()
    {
        /** @var QuoteFactory $quoteFactory */
        $quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
        /** @var ItemFactory $quoteItemFactory */
        $quoteItemFactory = Bootstrap::getObjectManager()->get(ItemFactory::class);
        /** @var AddressFactory $quoteAddressFactory */
        $quoteAddressFactory = Bootstrap::getObjectManager()->get(AddressFactory::class);
        /** @var OrderResponseTypeInterface $orderResponseTypeFactory */
        $orderResponseTypeFactory = Bootstrap::getObjectManager()->get(OrderResponseTypeInterfaceFactory::class);
        /** @var ShippingExperienceInterfaceFactory $shippingExperienceFactory */
        $shippingExperienceFactory = Bootstrap::getObjectManager()->get(ShippingExperienceInterfaceFactory::class);

        $updatedAt = '0000';
        $createdAt = '1999';
        $quoteId = 808;

        $addressCountryId = 'DE';
        $addressRegionId = '91';
        $addressRegionCode = 'SAS';
        $addressStreet = 'Nonnenstraße 11';
        $addressCity = 'Leipzig';
        $addressPostalCode = '04229';

        $address = $quoteAddressFactory->create(['data' => [
            'country_id' => $addressCountryId,
            'region_id' => $addressRegionId,
            'region_code' => $addressRegionCode,
            'street' => $addressStreet,
            'city' => $addressCity,
            'postcode' => $addressPostalCode,
        ]]);

        // replace by mock in test, address handling is hard to provide
        $quote = $quoteFactory->create(['data' => [
            'id' => $quoteId,
            'store_id' => 1,
            'updated_at' => $updatedAt,
            'created_at' => $createdAt,
            'items_qty' => 1,
            'shipping_address' => $address,
            'billing_address' => $address,
        ]]);

        $quoteItem = $quoteItemFactory->create(['data' => ['qty' => 1]]);
        $quoteItem->setQuote($quote);

        $allItems = [$quoteItem];
        $currency = Bootstrap::getObjectManager()->create(Currency::class, ['data' => ['currency_code' => 'EUR']]);
        $rateRequest = new RateRequest([
            'all_items' => $allItems,
            'dest_country_id' => $addressCountryId,
            'dest_region_id' => $addressRegionId,
            'dest_region_code' => $addressRegionCode,
            'dest_street' => $addressStreet,
            'dest_city' => $addressCity,
            'dest_postcode' => $addressPostalCode,
            'package_currency' => $currency,
            'store_id' => '1',
            'website_id' => '1',
        ]);

        /** @var ShippingExperienceInterface $shippingExperience */
        $shippingExperience = $shippingExperienceFactory->create([
            ShippingExperienceInterface::CODE => 'foo',
            ShippingExperienceInterface::COST => '9.09',
            ShippingExperienceInterface::LABEL => 'Foo Bar',
        ]);
        /** @var OrderResponseTypeInterfaceFactory $orderResponseTypeFactory */
        $orderResponseType = $orderResponseTypeFactory->create(['data' => [
            OrderReferenceInterface::ENTITY_ID => '1234-abcd',
            OrderReferenceInterface::EXT_ORDER_ID => '5678-efgh',
            OrderReferenceInterface::ORDER_ID => 42,
            OrderReferenceInterface::SHIPPING_EXPERIENCES => [$shippingExperience],
        ]]);

        return [
            'request_1' => [
                $rateRequest,
                $orderResponseType,
            ]
        ];
    }
}
