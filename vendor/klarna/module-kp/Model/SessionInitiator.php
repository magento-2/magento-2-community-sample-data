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

use Klarna\Core\Exception as KlarnaException;
use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Api\SessionInitiatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SessionInitiator
 *
 * @package Klarna\Kp\Model
 */
class SessionInitiator implements SessionInitiatorInterface
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Constructor
     *
     * @param QuoteRepositoryInterface $quoteRepository
     * @param Session                  $session
     * @param LoggerInterface          $log
     * @param ScopeConfigInterface     $config
     */
    public function __construct(
        QuoteRepositoryInterface $quoteRepository,
        Session $session,
        LoggerInterface $log,
        ScopeConfigInterface $config
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->session = $session;
        $this->log = $log;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailable($quote, $code)
    {
        if (!$quote) {
            $quote = $this->checkQuote();
        }

        /** @var ResponseInterface $klarnaPayments */
        try {
            $klarnaPayments = $this->session->getApiResponse();
            if ($klarnaPayments === null) {
                $sessionId = $this->getSessionId($quote);
                $klarnaPayments = $this->session->init($sessionId);
            }
            return $klarnaPayments->isSuccessfull() && $this->checkMethodAvailable($quote, $code);
        } catch (NoSuchEntityException $e) {
            $this->log->error($e);
            return false;
        } catch (KlarnaApiException $e) {
            $this->log->error($e);
            return false;
        } catch (KlarnaException $e) {
            $this->log->error($e);
            return true;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     */
    private function checkQuote()
    {
        $quote = $this->session->getQuote();
        if (!$quote) {
            return null;
        }

        $version = $this->config->getValue('klarna/api/api_version', ScopeInterface::SCOPE_STORES, $quote->getStore());
        if (!in_array($version, ['kp_na', 'kp_eu'], true)) {
            return null;
        }
        return $quote;
    }

    /**
     * @param CartInterface $quote
     * @return string
     */
    private function getSessionId(CartInterface $quote)
    {
        try {
            return $this->quoteRepository->getActiveByQuote($quote)->getSessionId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Check to ensure that this payment method is in the list of methods returned by API
     *
     * @param CartInterface $quote
     * @param string        $code
     * @return bool
     * @throws NoSuchEntityException
     */
    private function checkMethodAvailable(CartInterface $quote, $code)
    {
        $kQuote = $this->quoteRepository->getActiveByQuote($quote);
        return (in_array($code, $kQuote->getPaymentMethods()));
    }
}
