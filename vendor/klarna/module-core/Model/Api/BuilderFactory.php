<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Api;

/**
 * Class BuilderFactory
 *
 * @package Klarna\Core\Model\Api
 */
class BuilderFactory
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
        $method = $this->objectManager->create($className);
        if (!$method instanceof \Klarna\Core\Api\BuilderInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Klarna\Core\Api\BuilderInterface', $className)
            );
        }
        return $method;
    }
}
