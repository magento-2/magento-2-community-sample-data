<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Checkout\Orderline;

/**
 * Class OrderLineFactory
 *
 * @package Klarna\Core\Model\Checkout\Orderline
 */
class OrderLineFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Creates new instances of API models
     *
     * @param string $className
     * @return \Klarna\Core\Api\BuilderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className)
    {
        $method = $this->objectManager->get($className);
        if (!$method instanceof \Klarna\Core\Api\OrderLineInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Klarna\Core\Api\OrderLineInterface', $className)
            );
        }
        return $method;
    }
}
