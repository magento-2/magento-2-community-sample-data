<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Gateway\Command;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Api\OrderRepositoryInterface;
use Klarna\Core\Model\Api\Builder;
use Klarna\Core\Model\OrderFactory;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Authorize
 *
 * @package Klarna\Kp\Gateway\Command
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Authorize implements CommandInterface
{
    /**
     * Fraud status types
     */
    const FRAUD_STATUS_ACCEPTED = 'ACCEPTED';
    const FRAUD_STATUS_REJECTED = 'REJECTED';
    const FRAUD_STATUS_PENDING  = 'PENDING';

    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var OrderFactory
     */
    private $klarnaOrderFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $klarnaOrderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $mageQuoteRepository;

    /**
     * @var QuoteRepositoryInterface
     */
    private $klarnaQuoteRepository;

    /**
     * @var CreditApiInterface
     */
    private $api;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $mageOrderRepository;

    /**
     * Authorize constructor.
     *
     * @param QuoteRepositoryInterface                    $klarnaQuoteRepository
     * @param CartRepositoryInterface                     $quoteRepository
     * @param CreditApiInterface                          $api
     * @param OrderRepositoryInterface                    $klarnaOrderRepository
     * @param OrderFactory                                $klarnaOrderFactory
     * @param BuilderInterface                            $builder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $mageOrderRepository
     * @internal param OrderRepositoryInterface $orderRepository
     * @internal param OrderFactory $orderFactory
     */
    public function __construct(
        QuoteRepositoryInterface $klarnaQuoteRepository,
        CartRepositoryInterface $quoteRepository,
        CreditApiInterface $api,
        OrderRepositoryInterface $klarnaOrderRepository,
        OrderFactory $klarnaOrderFactory,
        BuilderInterface $builder,
        \Magento\Sales\Api\OrderRepositoryInterface $mageOrderRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->api = $api;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->klarnaOrderRepository = $klarnaOrderRepository;
        $this->klarnaOrderFactory = $klarnaOrderFactory;
        $this->builder = $builder;
        $this->mageQuoteRepository = $quoteRepository;
        $this->mageOrderRepository = $mageOrderRepository;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return null|Command\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Klarna\Core\Exception
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws CommandException
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $payment->getOrder();
        $quote = $this->mageQuoteRepository->get($order->getQuoteId());
        $klarnaQuote = $this->getKlarnaQuote($quote);
        /** @var RequestInterface $data */
        $data = $this->builder->setObject($quote)->generateRequest(Builder::GENERATE_TYPE_PLACE)->getRequest();
        $authorizationToken = $klarnaQuote->getAuthorizationToken();
        $result = $this->getKpApi()->placeOrder($authorizationToken, $data, $klarnaQuote->getSessionId());

        if (!$result->isSuccessfull()) {
            $response = $this->getKpApi()->cancelOrder($authorizationToken, $klarnaQuote->getSessionId());
            if (!$response->isSuccessfull()) {
                $message = $response->getMessage()
                    ?: __('Unable to release authorization for the token %1', $authorizationToken);
                throw new \Klarna\Core\Model\Api\Exception($message);
            }
            throw new \Klarna\Core\Exception(__('Unable to authorize payment for this order.'));
        }

        switch ($result->getFraudStatus()) {
            case self::FRAUD_STATUS_REJECTED:
                $payment->setIsFraudDetected(true);
                break;
            case self::FRAUD_STATUS_PENDING:
                $payment->setIsTransactionPending(true);
                break;
        }

        $klarnaOrder = $this->klarnaOrderFactory->create();
        $payment->getMethodInstance()->setCode(Kp::METHOD_CODE);
        $order->getPayment()->setMethod(Kp::METHOD_CODE);

        $this->mageOrderRepository->save($order);
        $klarnaOrder->setData([
            'klarna_order_id' => $result->getOrderId(),
            'reservation_id'  => $result->getOrderId(),
            'session_id'      => $klarnaQuote->getSessionId(),
            'order_id'        => $order->getId()
        ]);
        $this->klarnaOrderRepository->save($klarnaOrder);

        if (!$klarnaOrder->getId() || !$klarnaOrder->getReservationId()) {
            throw new \Klarna\Core\Exception(__('Unable to authorize payment for this order.'));
        }

        $payment->setTransactionId($result->getOrderId())->setIsTransactionClosed(0);
        return null;
    }

    /**
     * Get Klarna quote for a sales quote
     *
     * @param CartInterface $quote
     *
     * @return \Klarna\Kp\Api\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getKlarnaQuote(CartInterface $quote)
    {
        return $this->klarnaQuoteRepository->getActiveByQuote($quote);
    }

    /**
     * Get Klarna payments api class
     *
     * @return CreditApiInterface
     */
    private function getKpApi()
    {
        return $this->api;
    }
}
