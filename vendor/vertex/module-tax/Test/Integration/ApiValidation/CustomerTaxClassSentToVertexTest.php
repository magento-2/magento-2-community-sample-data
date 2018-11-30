<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Test\Integration\ApiValidation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterfaceFactory;
use Magento\Checkout\Api\TotalsInformationManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Vertex\Tax\Test\Integration\Builder\CartBuilder;
use Vertex\Tax\Test\Integration\Builder\CustomerBuilder;
use Vertex\Tax\Test\Integration\Builder\ProductBuilder;
use Vertex\Tax\Test\Integration\Builder\TaxClassBuilder;
use Vertex\Tax\Test\Integration\TestCase;

/**
 * Ensure that when totals are collected our tax request being sent to Vertex also sends the Customer's Tax Class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerTaxClassSentToVertexTest extends TestCase
{
    const TAX_CLASS_NAME = 'Testable Tax Class';

    /** @var AddressInterfaceFactory */
    private $addressFactory;

    /** @var CartBuilder */
    private $cartBuilder;

    /** @var CustomerBuilder */
    private $customerBuilder;

    /** @var GroupInterfaceFactory */
    private $customerGroupFactory;

    /** @var GroupRepositoryInterface */
    private $customerGroupRepository;

    /** @var ProductBuilder */
    private $productBuilder;

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
        $this->customerGroupFactory = $this->getObject(GroupInterfaceFactory::class);
        $this->customerGroupRepository = $this->getObject(GroupRepositoryInterface::class);
        $this->customerBuilder = $this->getObject(CustomerBuilder::class);
        $this->productBuilder = $this->getObject(ProductBuilder::class);
        $this->taxClassBuilder = $this->getObject(TaxClassBuilder::class);
        $this->cartBuilder = $this->getObject(CartBuilder::class);
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
    public function testOutgoingRequestContainsCustomerTaxClass()
    {
        $productTaxClassId = $this->createTaxClass('Testable Product Tax Class', 'PRODUCT');
        $customerTaxClassId = $this->createTaxClass(static::TAX_CLASS_NAME);
        $product = $this->createProduct($productTaxClassId);
        $customer = $this->createCustomer($customerTaxClassId);
        $cart = $this->createCartWithProduct($product, $customer->getId());

        $soapClient = $this->createPartialMock(\SoapClient::class, ['CalculateTax70']);
        $soapClient->expects($this->atLeastOnce())
            ->method('CalculateTax70')
            ->with(
                $this->callback(
                    function (\stdClass $request) {
                        $customerData = $request->QuotationRequest->Customer;
                        if (empty($customerData->CustomerCode) || empty($customerData->CustomerCode->classCode)) {
                            $this->fail(
                                'Customer with tax class "' . static::TAX_CLASS_NAME . '" not found in Vertex Request:'
                                . PHP_EOL
                                . print_r($request, true)
                            );
                            return false;
                        }

                        $this->assertEquals(static::TAX_CLASS_NAME, $customerData->CustomerCode->classCode);
                        return true;
                    }
                )
            )
            ->willReturn(new \stdClass());
        $this->getSoapFactory()->setSoapClient($soapClient);

        $address = $this->createShippingAddress($customer->getId());

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
     * @param int $customerId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createCartWithProduct(ProductInterface $product, $customerId)
    {
        return $this->cartBuilder->setItems()
            ->addItem($product)
            ->create($customerId);
    }

    /**
     * Create and save our test's needed Customer
     *
     * @param int|string $taxClassId
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createCustomer($taxClassId)
    {
        $groupId = $this->createCustomerGroup($taxClassId);

        return $this->customerBuilder->createCustomer(
            function (CustomerInterface $customer) use ($groupId) {
                $customer->setFirstname('John');
                $customer->setLastname('Doe');
                $customer->setEmail('jdoe@host.local');
                $customer->setGroupId($groupId);

                return $customer;
            }
        );
    }

    /**
     * Create a new customer group for the given tax class ID.
     *
     * @param $taxClassId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createCustomerGroup($taxClassId)
    {
        $group = $this->customerGroupFactory->create();

        $group->setCode('Test Customer Group');
        $group->setTaxClassId($taxClassId);

        return $this->customerGroupRepository->save($group)->getId();
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
                $product->setSku('TEST');
                $product->setPrice(5.00);
                $product->setVisibility(Visibility::VISIBILITY_BOTH);
                $product->setStatus(Status::STATUS_ENABLED);
                $product->setTypeId(Type::TYPE_SIMPLE);
                $product->setAttributeSetId(4);
                $product->setCustomAttribute('tax_class_id', $taxClassId);

                return $product;
            }
        );
    }

    /**
     * Create a shipping address for our order
     *
     * @param int|null $customerId
     * @return AddressInterface
     */
    private function createShippingAddress($customerId = null)
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->create();
        $address->setCustomerId($customerId);
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
     * @param string $type
     * @return string Tax Class ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createTaxClass($taxClassName, $type = 'CUSTOMER')
    {
        return $this->taxClassBuilder->createTaxClass($taxClassName, $type);
    }
}
