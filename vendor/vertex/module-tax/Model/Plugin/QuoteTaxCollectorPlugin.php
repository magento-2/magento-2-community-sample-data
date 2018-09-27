<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Vertex\Tax\Model\Calculator;
use Vertex\Tax\Model\Calculation\VertexCalculator\TaxAddressResolver;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\TaxRegistry;
use Magento\Tax\Model\Sales\Total\Quote\Tax;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Provide tax calculation services to the sales tax collector.
 */
class QuoteTaxCollectorPlugin
{
    /** @var Config */
    private $config;

    /** @var TaxAddressResolver */
    private $taxAddressResolver;

    /** @var Calculator */
    private $taxCollectorService;

    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * @param Config $config
     * @param Calculator $taxCollectorService
     * @param TaxAddressResolver $taxAddressResolver
     */
    public function __construct(
        Config $config,
        Calculator $taxCollectorService,
        TaxAddressResolver $taxAddressResolver,
        TaxRegistry $taxRegistry
    ) {
        $this->config = $config;
        $this->taxCollectorService = $taxCollectorService;
        $this->taxAddressResolver = $taxAddressResolver;
        $this->taxRegistry = $taxRegistry;
    }

    /**
     * Pre-calculate quote item taxes when Vertex is enabled.
     *
     * @param Tax $subject
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for plugin.
     */
    public function beforeCollect(
        Tax $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        if ($this->canUseVertex($quote)) {
            /** @var AddressInterface|null $taxAddress */
            $taxAddress = $this->taxAddressResolver->resolve(
                $quote->getBillingAddress(),
                $quote->getShippingAddress(),
                $quote->isVirtual(),
                $quote->getCustomerId()
            );

            if ($this->isValidAddress($taxAddress)) {
                $this->calculateTax($taxAddress, $quote->getCustomerGroupId());
            }
        }

        return null;
    }

    /**
     * Perform Vertex tax calculation.
     *
     * @param AddressInterface $taxAddress
     * @param int|null $customerGroupId
     * @return void
     */
    private function calculateTax(AddressInterface $taxAddress, $customerGroupId = null)
    {
        /** @var \Vertex\Tax\Model\TaxQuote\TaxQuoteResponse $result */
        $result = $this->taxCollectorService->calculateTax($taxAddress, $customerGroupId);

        if ($result === false) {
            $this->taxRegistry->registerError(
                __(
                    'Unable to calculate taxes. '.
                    'This could be caused by an invalid address provided in checkout.'
                )
            );
        } else {
            $this->taxRegistry->registerTaxes($result);
        }
    }

    /**
     * Determine whether Vertex tax calculation services may be used.
     *
     * @param Quote $quote
     * @return bool
     */
    private function canUseVertex(Quote $quote)
    {
        $storeId = $quote->getStoreId();

        return $this->config->isVertexActive($storeId)
            && $this->config->useVertexAlgorithm($storeId);
    }

    /**
     * Determine whether the given address is valid for tax calculation.
     *
     * This is a pre-flight check. Vertex may not provide calculation services without a country, region, and postcode.
     *
     * @param AddressInterface|null $address
     * @return boolean
     */
    private function isValidAddress($address)
    {
        return $address instanceof AddressInterface
            && $address->getCountryId()
            && (
                $address->getRegionId()
                || ($address->getRegion() instanceof RegionInterface && $address->getRegion()->getRegionId())
            )
            && $address->getPostcode();
    }
}
