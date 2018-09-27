<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Model\Api;

/**
 * Class Factory
 *
 * @package Klarna\Ordermanagement\Model\Api
 */
class Factory
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
     * @return \Klarna\Ordermanagement\Api\ApiInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className)
    {
        $method = $this->objectManager->create($className);
        if (!$method instanceof \Klarna\Ordermanagement\Api\ApiInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Klarna\Ordermanagement\Api\ApiInterface', $className)
            );
        }
        return $method;
    }
}
