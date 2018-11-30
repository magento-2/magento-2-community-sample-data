<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data\InvoiceRequestBuilder;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Vertex\Data\LineItemInterfaceFactory;
use Vertex\Services\Invoice\RequestInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Processes positive and negative adjustments added to a creditmemo
 */
class CreditmemoAdjustmentProcessor implements CreditmemoProcessorInterface
{
    /** @var TaxClassNameRepository */
    private $classNameRepository;

    /** @var Config */
    private $config;

    /** @var LineItemInterfaceFactory */
    private $lineItemFactory;

    /**
     * @param LineItemInterfaceFactory $lineItemFactory
     * @param Config $config
     * @param TaxClassNameRepository $classNameRepository
     */
    public function __construct(
        LineItemInterfaceFactory $lineItemFactory,
        Config $config,
        TaxClassNameRepository $classNameRepository
    ) {
        $this->lineItemFactory = $lineItemFactory;
        $this->config = $config;
        $this->classNameRepository = $classNameRepository;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, CreditmemoInterface $creditmemo)
    {
        $lineItems = $request->getLineItems();

        $adjustmentPositive = $creditmemo->getBaseAdjustmentPositive(); // additional refund
        $adjustmentNegative = $creditmemo->getBaseAdjustmentNegative(); // fee

        if ($adjustmentPositive >= 0) {
            $lineItem = $this->lineItemFactory->create();
            $lineItem->setUnitPrice(-1 * $adjustmentPositive);
            $lineItem->setExtendedPrice(-1 * $adjustmentPositive);
            $lineItem->setQuantity(1);
            $lineItem->setProductCode($this->config->getCreditmemoAdjustmentPositiveCode($creditmemo->getStoreId()));
            $lineItem->setProductClass(
                $this->classNameRepository->getById(
                    $this->config->getCreditmemoAdjustmentPositiveClass($creditmemo->getStoreId())
                )
            );

            $lineItems[] = $lineItem;
        }

        if ($adjustmentNegative >= 0) {
            $lineItem = $this->lineItemFactory->create();
            $lineItem->setUnitPrice($adjustmentNegative);
            $lineItem->setExtendedPrice($adjustmentNegative);
            $lineItem->setQuantity(1);
            $lineItem->setProductCode($this->config->getCreditmemoAdjustmentFeeCode($creditmemo->getStoreId()));
            $lineItem->setProductClass(
                $this->classNameRepository->getById(
                    $this->config->getCreditmemoAdjustmentFeeClass($creditmemo->getStoreId())
                )
            );

            $lineItems[] = $lineItem;
        }

        $request->setLineItems($lineItems);
        return $request;
    }
}
