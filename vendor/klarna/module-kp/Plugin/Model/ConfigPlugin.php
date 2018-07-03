<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Plugin\Model;

use Klarna\Core\Model\Config;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigPlugin
 *
 * @package Klarna\Kp\Plugin\Model
 */
class ConfigPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * ConfigPlugin constructor.
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param Config         $subject
     * @param bool           $result
     * @param StoreInterface $store
     * @return bool
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterKlarnaEnabled(Config $subject, $result, $store = null)
    {
        if ($result) {
            return $result; // No need to check any further, someone already said yes (true)
        }
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return $this->config->isSetFlag(
            sprintf('payment/%s/active', Kp::METHOD_CODE),
            $scope,
            $store
        );
    }
}
