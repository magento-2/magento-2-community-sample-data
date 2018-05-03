<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Plugin\Model\Checkout\Orderline;

use Klarna\Core\Model\Checkout\Orderline\Collector;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CollectorPlugin
 *
 * @package Klarna\Kp\Plugin\Model\Checkout\Orderline
 */
class CollectorPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * CollectorPlugin constructor.
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param Collector      $subject
     * @param bool           $result
     * @param StoreInterface $store
     * @return bool
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterIsKlarnaActive(Collector $subject, $result, $store)
    {
        if ($result) {
            return $result; // No need to check any further, someone already said yes (true)
        }
        return $this->config->isSetFlag(
            sprintf('payment/%s/active', Kp::METHOD_CODE),
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }
}
