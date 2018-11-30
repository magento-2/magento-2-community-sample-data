<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Fpt;

/**
 * Class Rate
 * @package Klarna\Core\Model\Fpt
 */
class Rate
{

    /** @var Validation $validation */
    private $validation;

    /**
     * Rate Constructor
     *
     * @param Validation $validation
     */
    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Get total FPT tax for all items on order/quote/invoice/creditmemo
     *
     * @param \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote $object $object
     * @return array
     */
    public function getFptTax($object)
    {
        $totalTax = 0;
        $name = [];
        $reference = [];

        foreach ($object->getAllItems() as $item) {
            if (($item instanceof \Magento\Sales\Model\Order\Invoice\Item
                    || $item instanceof \Magento\Sales\Model\Order\Creditmemo\Item)
                && !$this->validation->isValidOrderItem($item, $object)
            ) {
                continue;
            }

            if (!$this->validation->isValidQuoteItem($item, $object)) {
                continue;
            }

            $totalTax += $item->getWeeeTaxAppliedRowAmount();

            $weee = json_decode($item->getWeeeTaxApplied(), true);
            foreach ($weee as $tax) {
                $name[] = $tax['title'];
                $reference[] = $tax['title'];
            }
        }

        return [
            'tax' => $totalTax,
            'name' => array_unique($name),
            'reference' => array_unique($reference)
        ];
    }
}
