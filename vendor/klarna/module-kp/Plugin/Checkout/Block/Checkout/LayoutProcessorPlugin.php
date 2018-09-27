<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Plugin\Checkout\Block\Checkout;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class LayoutProcessorPlugin
 *
 * @package Klarna\Kp\Plugin\Checkout\Block\Checkout
 */
class LayoutProcessorPlugin
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * LayoutProcessorPlugin constructor.
     *
     * @param Session                  $session
     * @param ScopeConfigInterface     $config
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Session $session,
        ScopeConfigInterface $config,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->session = $session;
        $this->quoteRepository = $quoteRepository;
        $this->config = $config;
    }

    /**
     * Checkout LayoutProcessor before process plugin.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param array                                            $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $configuration = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['renders']['children'];

        if (!isset($configuration)) {
            return [$jsLayout];
        }

        if (!isset($configuration['klarna'])) {
            return [$jsLayout];
        }
        try {
            $quote = $this->session->getQuote();
            if (!$this->config->isSetFlag(
                sprintf('payment/%s/active', Kp::METHOD_CODE),
                ScopeInterface::SCOPE_STORES,
                $quote->getStore()
            )
            ) {
                return [$jsLayout];
            }
            $methods = $this->quoteRepository->getActiveByQuote($quote)->getPaymentMethods();
        } catch (NoSuchEntityException $e) {
            $methods = [];
        }
        foreach ($methods as $method) {
            $configuration['klarna']['methods'][$method] = $configuration['klarna']['methods']['klarna_kp'];
        }
        return [$jsLayout];
    }
}
