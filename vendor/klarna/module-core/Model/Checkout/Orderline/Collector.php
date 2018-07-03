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

use Klarna\Core\Api\OrderLineInterface;
use Klarna\Core\Exception as KlarnaException;
use Klarna\Core\Helper\KlarnaConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Psr\Log\LoggerInterface;

/**
 * Klarna total collector
 */
class Collector
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * Sorted models
     *
     * @var array
     */
    private $collectors = [];
    /**
     * @var OrderLineFactory
     */
    private $orderLineFactory;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Init corresponding models
     *
     * @param ScopeConfigInterface $config
     * @param KlarnaConfig         $klarnaConfig
     * @param OrderLineFactory     $orderLineFactory
     * @param LoggerInterface      $logger
     */
    public function __construct(
        ScopeConfigInterface $config,
        KlarnaConfig $klarnaConfig,
        OrderLineFactory $orderLineFactory,
        LoggerInterface $logger
    ) {
        $this->orderLineFactory = $orderLineFactory;
        $this->config = $config;
        $this->klarnaConfig = $klarnaConfig;
        $this->logger = $logger;
    }

    /**
     * Get models for calculation logic
     *
     * @param StoreInterface $store
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollectors(StoreInterface $store)
    {
        $this->initCollectors($store);
        return $this->collectors;
    }

    /**
     * Initialize models configuration and objects
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function initCollectors($store)
    {
        if (!$this->isKlarnaActive($store)) {
            return $this; // No Klarna methods enabled
        }

        try {
            $checkoutType = $this->klarnaConfig->getCheckoutType($store);
            $totalsConfig = $this->klarnaConfig->getOrderlines($checkoutType);
        } catch (KlarnaException $e) {
            $this->logger->debug($e);
            return $this;
        }

        if (!$totalsConfig) {
            return $this;
        }

        foreach ($totalsConfig as $totalCode => $totalConfig) {
            $class = $totalConfig['class'];
            if (!empty($class)) {
                $this->collectors[$totalCode] = $this->initModelInstance($class, $totalCode);
            }
        }

        return $this;
    }

    /**
     * @param StoreInterface $store
     * @return bool
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function isKlarnaActive($store = null)
    {
        // It is expected that this method will have plugins added by other modules. $store is required in those cases.
        return false;
    }

    /**
     * Init model class by configuration
     *
     * @param string $class
     * @param string $totalCode
     * @return OrderLineInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function initModelInstance($class, $totalCode)
    {
        /** @var OrderLineInterface $model */
        $model = $this->orderLineFactory->create($class);
        $model->setCode($totalCode);
        return $model;
    }
}
