<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\Method\Factory;
use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PaymentMethodList
 *
 * @package Klarna\Kp\Model
 */
class PaymentMethodList implements \Klarna\Kp\Api\PaymentMethodListInterface
{
    /**
     * Factory for payment method models
     *
     * @var Factory
     */
    private $methodFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var \Klarna\Kp\Model\Payment\Kp[]
     */
    private $paymentMethods = [];

    /**
     * PaymentMethodList constructor.
     *
     * @param Factory                  $methodFactory
     * @param QuoteRepositoryInterface $quoteRepository
     * @param LoggerInterface          $log
     */
    public function __construct(
        Factory $methodFactory,
        QuoteRepositoryInterface $quoteRepository,
        LoggerInterface $log
    ) {
        $this->methodFactory = $methodFactory;
        $this->quoteRepository = $quoteRepository;
        $this->log = $log;
    }

    /**
     * {@inheritdoc}
     */
    public function getKlarnaMethodCodes(CartInterface $quote = null)
    {
        if (!$quote) {
            return [];
        }
        try {
            return $this->quoteRepository->getActiveByQuote($quote)->getPaymentMethods();
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod($method)
    {
        if (!isset($this->paymentMethods[$method])) {
            $this->paymentMethods[$method] = $this->methodFactory->create(\Klarna\Kp\Model\Payment\Kp::class)
                ->setCode($method);
        }
        return $this->paymentMethods[$method];
    }

    /**
     * {@inheritdoc}
     */
    public function getKlarnaMethodInfo(CartInterface $quote)
    {
        try {
            return $this->quoteRepository->getActiveByQuote($quote)->getPaymentMethodInfo();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
