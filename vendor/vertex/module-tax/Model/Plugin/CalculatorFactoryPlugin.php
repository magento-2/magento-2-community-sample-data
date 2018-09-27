<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Tax\Model\Calculation\CalculatorFactory;
use Vertex\Tax\Model\Calculation\VertexCalculatorFactory;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\TaxRegistry;

/**
 * Performs everything necessary for Vertex to Calculate Taxes during actual calculation
 *
 * @see \Magento\Tax\Model\Calculation\CalculatorFactory
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculatorFactoryPlugin
{
    /** @var \Vertex\Tax\Model\Calculation\VertexCalculator[] */
    private $calculator = [];

    /** @var Config */
    private $config;

    /** @var VertexCalculatorFactory */
    private $vertexCalculatorFactory;

    /** @var TaxRegistry */
    private $taxRegistry;

    /**
     * @param Config $config
     * @param VertexCalculatorFactory $vertexCalculatorFactory
     * @param TaxRegistry $taxRegistry
     */
    public function __construct(
        Config $config,
        VertexCalculatorFactory $vertexCalculatorFactory,
        TaxRegistry $taxRegistry
    ) {
        $this->taxRegistry = $taxRegistry;
        $this->config = $config;
        $this->vertexCalculatorFactory = $vertexCalculatorFactory;
    }

    /**
     * Determine whether or not we should use Vertex to calculate Tax, and calculates & stores tax if so
     *
     * MEQP2 Warning: Unused parameters expected from plugins
     *
     * @see CalculatorFactory::create()
     *
     * @param CalculatorFactory $subject
     * @param \Closure $proceed
     * @param string $type
     * @param int $storeId
     * @param CustomerAddressInterface|null $billingAddress
     * @param CustomerAddressInterface|null $shippingAddress
     * @param null|int $customerTaxClassId
     * @param null|int $customerId
     * @return \Magento\Tax\Model\Calculation\AbstractCalculator
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Unused parameters required for plugin.
     */
    public function aroundCreate(
        CalculatorFactory $subject,
        \Closure $proceed,
        $type,
        $storeId,
        CustomerAddressInterface $billingAddress = null,
        CustomerAddressInterface $shippingAddress = null,
        $customerTaxClassId = null,
        $customerId = null
    ) {
        if ($this->taxRegistry->hasTaxes()
            || $type === Config::CALC_UNIT_VERTEX // @todo remove in 2.2.6
        ) {
            return $this->getVertexCalculator($storeId);
        }

        return $proceed($type, $storeId, $billingAddress, $shippingAddress, $customerTaxClassId, $customerId);
    }

    /**
     * Get a Vertex calculator instance for the given store.
     *
     * @param $storeId
     * @return \Vertex\Tax\Model\Calculation\VertexCalculator
     */
    private function getVertexCalculator($storeId)
    {
        if (!isset($this->calculator[$storeId])) {
            $this->calculator[$storeId] = $this->vertexCalculatorFactory->create(['storeId' => $storeId]);
        }

        return $this->calculator[$storeId];
    }
}
