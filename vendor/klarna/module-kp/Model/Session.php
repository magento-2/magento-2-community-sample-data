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

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\QuoteFactory as KlarnaQuoteFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Session
 *
 * @package Klarna\Kp\Model
 */
class Session
{
    /**
     * Request Builder
     *
     * @var BuilderInterface
     */
    private $builder;

    /**
     * Klarna Payments API
     *
     * @var CreditApiInterface
     */
    private $api;

    /**
     * Klarna Quote Repository
     *
     * @var QuoteRepositoryInterface
     */
    private $kQuoteRepository;

    /**
     * Klarna Quote Factory
     *
     * @var KlarnaQuoteFactory
     */
    private $klarnaQuoteFactory;

    /**
     * @var ResponseInterface
     */
    private $apiResponse;

    /**
     * @var QuoteInterface
     */
    private $klarnaQuote;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * Session constructor.
     *
     * @param \Magento\Checkout\Model\Session $session
     * @param CreditApiInterface              $api
     * @param BuilderInterface                $builder
     * @param QuoteRepositoryInterface        $kQuoteRepository
     * @param KlarnaQuoteFactory              $klarnaQuoteFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        CreditApiInterface $api,
        BuilderInterface $builder,
        QuoteRepositoryInterface $kQuoteRepository,
        KlarnaQuoteFactory $klarnaQuoteFactory
    ) {
        $this->api = $api;
        $this->builder = $builder;
        $this->kQuoteRepository = $kQuoteRepository;
        $this->klarnaQuoteFactory = $klarnaQuoteFactory;
        $this->session = $session;
    }

    /**
     * Initialize Session
     *
     * @param string $sessionId
     * @return ResponseInterface
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Klarna\Core\Exception
     */
    public function init($sessionId = null)
    {
        $klarnaResponse = $this->getApiResponse();
        if (!$klarnaResponse) {
            $klarnaResponse = $this->requestKlarnaSession($sessionId);

            $klarnaQuote = $this->generateKlarnaQuote($klarnaResponse);
            $this->setKlarnaQuote($klarnaQuote);
            $this->setApiResponse($klarnaResponse);
        }
        return $klarnaResponse;
    }

    /**
     * Get API Response
     *
     * @return ResponseInterface
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }

    /**
     * Set API Response
     *
     * @param ResponseInterface $klarnaQuote
     * @return $this
     */
    public function setApiResponse($klarnaQuote)
    {
        $this->apiResponse = $klarnaQuote;
        return $this;
    }

    /**
     * Start a Klarna Session
     *
     * @param string $sessionId
     * @return ResponseInterface
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Klarna\Core\Exception
     */
    private function requestKlarnaSession($sessionId = null)
    {
        if (null === $sessionId) {
            return $this->initWithoutSession();
        }
        return $this->initWithSession($sessionId);
    }

    /**
     * Create a new Klarna Session
     *
     * @return ResponseInterface
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Klarna\Core\Exception
     */
    private function initWithoutSession()
    {
        $data = $this->getGeneratedCreateRequest();
        $klarnaResponse = $this->initKlarnaQuote($data);

        return $klarnaResponse;
    }

    /**
     * Get the create request
     *
     * @return RequestInterface|string[]
     * @throws \Klarna\Core\Exception
     */
    private function getGeneratedCreateRequest()
    {
        return $this->builder->setObject($this->getQuote())->generateRequest(BuilderInterface::GENERATE_TYPE_CREATE)
            ->getRequest();
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->session->getQuote();
    }

    /**
     * Initialize Klarna Quote
     *
     * @param RequestInterface $data
     * @return \Klarna\Kp\Api\Data\ResponseInterface
     */
    private function initKlarnaQuote(RequestInterface $data)
    {
        try {
            $klarnaQuote = $this->kQuoteRepository->getActiveByQuote($this->getQuote());
            $sessionId = $klarnaQuote->getSessionId();
            if (null === $sessionId) {
                $this->kQuoteRepository->markInactive($klarnaQuote);
                return $this->api->createSession($data);
            }
            $resp = $this->updateOrCreateSession($sessionId, $data);
            if ($resp->getSessionId() !== $sessionId) {
                $this->kQuoteRepository->markInactive($klarnaQuote);
            }
            return $resp;
        } catch (NoSuchEntityException $e) {
            return $this->api->createSession($data);
        }
    }

    /**
     * Update existing session. Create a new session if update fails
     *
     * @param string           $sessionId
     * @param RequestInterface $data
     * @return ResponseInterface
     */
    private function updateOrCreateSession($sessionId, RequestInterface $data)
    {
        $resp = $this->api->updateSession($sessionId, $data);
        if ($resp->isSuccessfull()) {
            return $resp;
        }
        return $this->api->createSession($data);
    }

    /**
     * Attempt to lookup existing session
     *
     * @param string $sessionId
     * @return ResponseInterface
     * @throws \Klarna\Core\Model\Api\Exception
     * @throws \Klarna\Core\Exception
     */
    private function initWithSession($sessionId)
    {
        $data = $this->getGeneratedCreateRequest();
        $klarnaResponse = $this->updateOrCreateSession($sessionId, $data);

        if (!$klarnaResponse->isSuccessfull()) {
            throw new KlarnaApiException(__('Unable to initialize Klarna payments session'));
        }
        return $klarnaResponse;
    }

    /**
     * Lookup or create Klarna Quote
     *
     * @param ResponseInterface $klarnaResponse
     * @return QuoteInterface
     */
    private function generateKlarnaQuote(ResponseInterface $klarnaResponse)
    {
        try {
            $klarnaQuote = $this->kQuoteRepository->getBySessionId($klarnaResponse->getSessionId());
            $klarnaQuote->setPaymentMethods(
                $this->extractPaymentMethods($klarnaResponse->getPaymentMethodCategories())
            );
            $klarnaQuote->setPaymentMethodInfo($klarnaResponse->getPaymentMethodCategories());
            $this->kQuoteRepository->save($klarnaQuote);
            return $klarnaQuote;
        } catch (NoSuchEntityException $e) {
            return $this->createNewQuote($klarnaResponse);
        }
    }

    /**
     * @param $quoteData
     * @return mixed
     */
    private function extractPaymentMethods($categories)
    {
        $payment_methods = [];
        foreach ($categories as $category) {
            $payment_methods[] = 'klarna_' . $category['identifier'];
        }
        return implode(',', $payment_methods);
    }

    /**
     * Create a new Klarna quote object
     *
     * @param ResponseInterface $resp
     * @return QuoteInterface
     */
    private function createNewQuote(ResponseInterface $resp)
    {
        if (!$this->getQuote()->getId()) {
            throw new KlarnaApiException(__('Unable to initialize Klarna payments session'));
        }

        /** @var QuoteInterface $klarnaQuote */
        $klarnaQuote = $this->klarnaQuoteFactory->create();
        $klarnaQuote->setSessionId($resp->getSessionId());
        $klarnaQuote->setClientToken($resp->getClientToken());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setQuoteId($this->getQuote()->getId());
        $klarnaQuote->setPaymentMethods($this->extractPaymentMethods($resp->getPaymentMethodCategories()));
        $klarnaQuote->setPaymentMethodInfo($resp->getPaymentMethodCategories());
        $this->kQuoteRepository->save($klarnaQuote);
        return $klarnaQuote;
    }

    /**
     * Get Klarna Quote
     *
     * @return \Klarna\Kp\Api\QuoteInterface
     */
    public function getKlarnaQuote()
    {
        return $this->klarnaQuote;
    }

    /**
     * Set Klarna Quote
     *
     * @param QuoteInterface $klarnaQuote
     * @return $this
     */
    public function setKlarnaQuote(QuoteInterface $klarnaQuote)
    {
        $this->klarnaQuote = $klarnaQuote;
        return $this;
    }
}
