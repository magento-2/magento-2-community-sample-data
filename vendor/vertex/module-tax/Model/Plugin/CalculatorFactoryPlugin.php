<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddress;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation\AbstractCalculator;
use Magento\Tax\Model\Calculation\CalculatorFactory;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\Calculation\VertexCalculatorFactory;
use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\QuoteProviderInterface;
use Vertex\Tax\Exception\TaxCalculationException;

/**
 * Performs everything necessary for Vertex to Calculate Taxes during actual calculation
 *
 * @see \Magento\Tax\Model\Calculation\CalculatorFactory
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculatorFactoryPlugin
{
    /**
     * Identifier constant for Vertex based calculation
     */
    const CALC_UNIT_VERTEX = 'VERTEX_UNIT_BASE_CALCULATION';

    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $registry;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var Config */
    private $config;

    /** @var VertexCalculatorFactory */
    private $vertexCalculatorFactory;

    /** @var Calculator */
    private $calculator;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var QuoteProviderInterface */
    private $quoteProvider;

    /**
     * @param LoggerInterface $logger
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param VertexCalculatorFactory $vertexCalculatorFactory
     * @param Calculator $calculator
     * @param CountryGuard $countryGuard
     * @param QuoteProviderInterface $quoteProvider
     */
    public function __construct(
        LoggerInterface $logger,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Config $config,
        VertexCalculatorFactory $vertexCalculatorFactory,
        Calculator $calculator,
        CountryGuard $countryGuard,
        QuoteProviderInterface $quoteProvider
    ) {
        $this->logger = $logger;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->vertexCalculatorFactory = $vertexCalculatorFactory;
        $this->calculator = $calculator;
        $this->countryGuard = $countryGuard;
        $this->quoteProvider = $quoteProvider;
    }

    /**
     * Determine whether or not we should use Vertex to calculate Tax, and calculates & stores tax if so
     *
     * MEQP2 Warning: Unused parameter $subtotal expected from plugins
     *
     * @see CalculatorFactory::create()
     *
     * @param CalculatorFactory $subject
     * @param \Closure $proceed
     * @param string $type
     * @param int $storeId
     * @param CustomerAddress|null $billingAddress
     * @param CustomerAddress|null $shippingAddress
     * @param null|int $customerTaxClassId
     * @param null|int $customerId
     * @return AbstractCalculator
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     */
    public function aroundCreate(
        CalculatorFactory $subject,
        \Closure $proceed,
        $type,
        $storeId,
        CustomerAddress $billingAddress = null,
        CustomerAddress $shippingAddress = null,
        $customerTaxClassId = null,
        $customerId = null
    ) {
        $address = $this->decideTaxAddress($billingAddress, $shippingAddress);

        if ($type === self::CALC_UNIT_VERTEX) {
            /** @var VertexCalculator $vertexCalculator */
            $vertexCalculator = $this->vertexCalculatorFactory->create(['storeId' => $storeId]);

            $this->setShippingAddress($vertexCalculator, $shippingAddress);
            $this->setBillingAddress($vertexCalculator, $billingAddress);
            $vertexCalculator->setCustomerTaxClassId($customerTaxClassId);
            $vertexCalculator->setCustomerId($customerId);

            if ($address && $this->canCalculateTax($address) && $this->taxAreaCheck($address)) {
                $this->prepareCalculator();
            }

            return $vertexCalculator;
        }

        return $proceed($type, $storeId, $billingAddress, $shippingAddress, $customerTaxClassId, $customerId);
    }

    /**
     * Prepare the registry with all the information necessary for the Vertex Tax Calculator to do it's job
     */
    private function prepareCalculator()
    {
        $itemsVertexTaxes = [];

        try {
            $quote = $this->quoteProvider->getQuote();

            if ($quote) {
                $taxAddress = $this->decideTaxAddress(
                    $quote->getBillingAddress(),
                    $quote->getShippingAddress(),
                    $quote->isVirtual()
                );
                
                $result = null;

                if ($taxAddress) {
                    $result = $this->calculator->calculateTax($taxAddress);
                }

                if ($result !== null && $result !== false) {
                    $itemsVertexTaxes = $result->getQuoteTaxedItems();
                } else {
                    throw new TaxCalculationException(__('Vertex was unable to provide tax calculation services.'));
                }
            }
        } catch (TaxCalculationException $error) {
            $this->registry->unregister(VertexCalculator::VERTEX_CALCULATION_ERROR);
            $this->registry->register(VertexCalculator::VERTEX_CALCULATION_ERROR, $error->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        $this->registry->unregister(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY);
        $this->registry->register(VertexCalculator::VERTEX_LINE_ITEM_TAX_KEY, $itemsVertexTaxes);
    }

    /**
     * Determine if an address can go through tax calculation
     *
     * @param CustomerAddress $address
     * @return boolean
     */
    private function taxAreaCheck(CustomerAddress $address)
    {
        if ($address->getId()
            && $address->getCity()
            && $this->countryGuard->isCountryIdServiceableByVertex($address->getCountryId())
        ) {
            try {
                $taxArea = $this->calculator->calculateTaxAreaIds($address);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                return false;
            }

            if (!$taxArea || $taxArea === null) {
                $this->logger->error("Tax Area not found for address {$address->getId()}");
                return false;
            }
        }
        return true;
    }

    /**
     * Set the shipping address on the calculator if not null
     *
     * @param VertexCalculator $calculator
     * @param CustomerAddress|null $shippingAddress
     */
    private function setShippingAddress(VertexCalculator $calculator, CustomerAddress $shippingAddress = null)
    {
        if ($shippingAddress !== null) {
            $calculator->setShippingAddress($shippingAddress);
        }
    }

    /**
     * Set the billing address on the calculator if not null
     *
     * @param VertexCalculator $calculator
     * @param CustomerAddress|null $billingAddress
     */
    private function setBillingAddress(VertexCalculator $calculator, CustomerAddress $billingAddress = null)
    {
        if ($billingAddress !== null) {
            $calculator->setBillingAddress($billingAddress);
        }
    }

    /**
     * Determine if tax can be calculated for an address
     *
     * @param CustomerAddress|null $address
     * @return bool
     */
    private function canCalculateTax(CustomerAddress $address = null)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if (!$this->config->isVertexActive($storeId)) {
            return false;
        }

        // Request was not sent. Address not specified.
        if (!$this->isAcceptableAddress($address)) {
            return false;
        }

        return true;
    }

    /**
     * Determine which address should be used for tax calculation
     *
     * @param CustomerAddress|QuoteAddress|null $billingAddress
     * @param CustomerAddress|QuoteAddress|null $shippingAddress
     * @param bool $isVirtual
     * @return CustomerAddress|QuoteAddress|null
     */
    private function decideTaxAddress($billingAddress = null, $shippingAddress = null, $isVirtual = false)
    {
        return $this->isAcceptableAddress($shippingAddress) && !$isVirtual ? $shippingAddress : $billingAddress;
    }

    /**
     * Determine if an address can be used for tax calculation
     *
     * @param CustomerAddress|QuoteAddress|null $address
     * @return bool
     */
    private function isAcceptableAddress($address = null)
    {
        return $address !== null
            && $address->getCountryId()
            && ($address->getRegionId() ||
                ($address->getRegion() instanceof RegionInterface && $address->getRegion()->getRegionId())
            )
            && $address->getPostcode();
    }
}
