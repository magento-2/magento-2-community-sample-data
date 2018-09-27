<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Plugin\Model;

use Klarna\Kp\Api\SessionInitiatorInterface;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\SessionInitiatorFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class MethodListPlugin
 *
 * @package Klarna\Kp\Plugin\Model
 */
class MethodListPlugin
{
    /**
     * @var SessionInitiatorFactory
     */
    private $sessInitFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * MethodListPlugin constructor.
     *
     * @param SessionInitiatorFactory $sessInitFactory
     * @param ScopeConfigInterface    $config
     */
    public function __construct(SessionInitiatorFactory $sessInitFactory, ScopeConfigInterface $config)
    {
        $this->sessInitFactory = $sessInitFactory;
        $this->config = $config;
    }

    /**
     * Ensure payment methods are initialized before first getting
     * list of available payment methods
     *
     * @param MethodList         $subject
     * @param CartInterface|null $quote
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     * @return array
     */
    public function beforeGetAvailableMethods(MethodList $subject, CartInterface $quote = null)
    {
        if ($this->isEnabled($quote)) {
            $this->getSessionInitiator()->checkAvailable($quote, Kp::METHOD_CODE);
        }
        return [$quote];
    }

    /**
     * Check to see if we should run or not
     *
     * @param CartInterface $quote
     * @return bool
     */
    private function isEnabled(CartInterface $quote = null)
    {
        if (!$quote) {
            return false;
        }
        $store = $quote->getStore();
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        if (!$this->config->isSetFlag('payment/' . Kp::METHOD_CODE . '/active', $scope, $store)) {
            return false;
        }
        return true;
    }

    /**
     * Get SessionInitiator instance
     *
     * @return SessionInitiatorInterface
     */
    private function getSessionInitiator()
    {
        return $this->sessInitFactory->create();
    }
}
