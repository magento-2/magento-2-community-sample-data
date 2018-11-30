<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\ApiValidation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterfaceFactory;
use Magento\Checkout\Api\TotalsInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Vertex\Tax\Test\Integration\Builder\GuestCartBuilder;
use Vertex\Tax\Test\Integration\Builder\ProductBuilder;
use Vertex\Tax\Test\Integration\Builder\TaxClassBuilder;
use Vertex\Tax\Test\Integration\TestCase;

/**
 * Ensure that when totals are collected our tax request being sent to Vertex also sends the Product's Tax Class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductTaxClassSentToVertexTest extends TestCase
{
    const PRODUCT_SKU = 'TEST';
    const TAX_CLASS_NAME = 'Testable Tax Class';

    /** @var AddressInterfaceFactory */
    private $addressFactory;

    /** @var GuestCartBuilder */
    private $guestCartBuilder;

    /** @var ProductBuilder */
    private $productBuilder;

    /** @var StockRegistryStorage */
    private $stockRegistryStorage;

    /** @var TaxClassBuilder */
    private $taxClassBuilder;

    /** @var TotalsInformationManagementInterface */
    private $totalManager;

    /** @var TotalsInformationInterfaceFactory */
    private $totalsInformationFactory;

    /**
     * Fetch objects necessary for running our test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->totalManager = $this->getObject(TotalsInformationManagementInterface::class);
        $this->totalsInformationFactory = $this->getObject(TotalsInformationInterfaceFactory::class);
        $this->addressFactory = $this->getObject(AddressInterfaceFactory::class);
        $this->stockRegistryStorage = $this->getObject(StockRegistryStorage::class);

        $this->productBuilder = $this->getObject(ProductBuilder::class);
        $this->taxClassBuilder = $this->getObject(TaxClassBuilder::class);
        $this->guestCartBuilder = $this->getObject(GuestCartBuilder::class);
    }

    /**
     * Ensure that when totals are collected our tax request being sent to Vertex also sends the Product's Tax Class
     *
     * @magentoConfigFixture default_store tax/vertex_settings/enable_vertex 1
     * @magentoConfigFixture default_store tax/vertex_settings/api_url https://example.org/CalculateTax70
     * @magentoDbIsolation enabled
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return void
     */
    public function testOutgoingRequestContainsProductTaxClass()
    {
        $taxClassId = $this->createTaxClass(static::TAX_CLASS_NAME);
        $product = $this->createProduct($taxClassId);
        $cart = $this->createCartWithProduct($product);

        $soapClient = $this->createPartialMock(\SoapClient::class, ['CalculateTax70']);
        $soapClient->expects($this->atLeastOnce())
            ->method('CalculateTax70')
            ->with(
                $this->callback(
                    function (\stdClass $request) {
                        $lineItems = $request->QuotationRequest->LineItem;
                        foreach ($lineItems as $lineItem) {
                            $product = $lineItem->Product;
                            if ($product->_ === static::PRODUCT_SKU) {
                                $this->assertEquals(static::TAX_CLASS_NAME, $product->productClass);
                                return true;
                            }
                        }
                        $this->fail(
                            'Product with SKU "' . static::PRODUCT_SKU . '" not found in Vertex Request:' . PHP_EOL
                            . print_r($request, true)
                        );
                        return false;
                    }
                )
            )
            ->willReturn(new \stdClass());
        $this->getSoapFactory()->setSoapClient($soapClient);

        $address = $this->createShippingAddress();

        /** @var TotalsInformationInterface $totalsInfo */
        $totalsInfo = $this->totalsInformationFactory->create();
        $totalsInfo->setAddress($address);
        $totalsInfo->setShippingCarrierCode('flatrate');
        $totalsInfo->setShippingMethodCode('flatrate');

        $this->totalManager->calculate($cart->getId(), $totalsInfo);
    }

    /**
     * Creates a guest cart containing 1 of the provided product
     *
     * @param ProductInterface $product
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createCartWithProduct(ProductInterface $product)
    {
        return $this->guestCartBuilder->setItems()
            ->addItem($product)
            ->create();
    }

    /**
     * Create and save our test's needed Product
     *
     * @param int|string $taxClassId
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function createProduct($taxClassId)
    {
        return $this->productBuilder->createProduct(
            function (ProductInterface $product) use ($taxClassId) {
                $product->setName('Example Product');
                $product->setSku(static::PRODUCT_SKU);
                $product->setPrice(5.00);
                $product->setVisibility(Visibility::VISIBILITY_BOTH);
                $product->setStatus(Status::STATUS_ENABLED);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setAttributeSetId(4);
                $product->setCustomAttribute('tax_class_id', $taxClassId);

                return $product;
            }
        );
    }

    /**
     * Create a shipping address for our order
     *
     * @return AddressInterface
     */
    private function createShippingAddress()
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->create();
        $address->setCity('West Chester');
        $address->setCountryId('US');
        $address->setFirstname('John');
        $address->setLastname('Doe');
        $address->setPostcode('19382');
        $address->setRegion('Pennsylvania');
        $address->setRegionCode('PA');
        $address->setRegionId(51);
        $address->setStreet(['233 West Gay St']);
        $address->setTelephone('1234567890');
        return $address;
    }

    /**
     * Create and save our test's needed tax class
     *
     * @param string $taxClassName
     * @return string Tax Class ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createTaxClass($taxClassName)
    {
        return $this->taxClassBuilder->createTaxClass($taxClassName, 'PRODUCT');
    }
}
