<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Payment;

use Klarna\Core\Helper\ConfigHelper;
use Klarna\Kp\Api\KlarnaPaymentMethodInterface;
use Klarna\Kp\Api\SessionInitiatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Locale\Resolver;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Adapter;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Klarna Payment
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) //TODO: Remove in 6.0 when deprecated methods are dropped
 */
class Kp implements MethodInterface, KlarnaPaymentMethodInterface
{
    const METHOD_CODE = ConfigHelper::KP_METHOD_CODE;

    /**
     * @deprecated 5.3.0
     */
    const KLARNA_LOGO_SLICE_IT = 'https://cdn.klarna.com/1.0/shared/image/generic/badge/%s/slice_it/standard/pink.svg';
    /**
     * @deprecated 5.3.0
     */
    // @codingStandardsIgnoreLine
    const KLARNA_LOGO_PAY_LATER = 'https://cdn.klarna.com/1.0/shared/image/generic/badge/%s/pay_later/standard/pink.svg';
    /**
     * @deprecated 5.3.0
     */
    const KLARNA_LOGO_PAY_NOW = 'https://cdn.klarna.com/1.0/shared/image/generic/badge/%s/pay_now/standard/pink.svg';
    /**
     * @deprecated 5.3.0
     */
    const KLARNA_LOGO = 'https://cdn.klarna.com/1.0/shared/image/generic/badge/%s/%s/standard/pink.svg';
    /**
     * @deprecated 5.3.0
     */
    const KLARNA_METHODS = [
        'klarna_pay_now',
        'klarna_pay_later',
        'klarna_pay_over_time',
        'klarna_direct_debit',
        'klarna_direct_bank_transfer'
    ];

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $code = 'klarna_kp';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var \Klarna\Kp\Model\SessionInitiatorFactory
     */
    private $sessionInitiatorFactory;

    /**
     * Kco constructor.
     *
     * @param Adapter                                  $adapter
     * @param Resolver                                 $resolver
     * @param ScopeConfigInterface                     $config
     * @param \Klarna\Kp\Model\SessionInitiatorFactory $sessionInitiatorFactory
     */
    public function __construct(
        Adapter $adapter,
        Resolver $resolver,
        ScopeConfigInterface $config,
        \Klarna\Kp\Model\SessionInitiatorFactory $sessionInitiatorFactory
    ) {
        $this->adapter = $adapter;
        $this->resolver = $resolver;
        $this->config = $config;
        $this->sessionInitiatorFactory = $sessionInitiatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive($storeId = null)
    {
        $scope = ($storeId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return $this->config->isSetFlag('payment/' . self::METHOD_CODE . '/active', $scope, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBlockType()
    {
        return $this->adapter->getFormBlockType();
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($storeId)
    {
        $this->adapter->setStore($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->adapter->getStore();
    }

    /**
     * {@inheritdoc}
     */
    public function canOrder()
    {
        return $this->adapter->canOrder();
    }

    /**
     * {@inheritdoc}
     */
    public function canAuthorize()
    {
        return $this->adapter->canAuthorize();
    }

    /**
     * {@inheritdoc}
     */
    public function canCapture()
    {
        return $this->adapter->canCapture();
    }

    /**
     * {@inheritdoc}
     */
    public function canCapturePartial()
    {
        return $this->adapter->canCapturePartial();
    }

    /**
     * {@inheritdoc}
     */
    public function canCaptureOnce()
    {
        return $this->adapter->canCaptureOnce();
    }

    /**
     * {@inheritdoc}
     */
    public function canRefund()
    {
        return $this->adapter->canRefund();
    }

    /**
     * {@inheritdoc}
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->adapter->canRefundPartialPerInvoice();
    }

    /**
     * {@inheritdoc}
     */
    public function canVoid()
    {
        return $this->adapter->canVoid();
    }

    /**
     * {@inheritdoc}
     */
    public function canUseInternal()
    {
        return $this->adapter->canUseInternal();
    }

    /**
     * {@inheritdoc}
     */
    public function canUseCheckout()
    {
        return $this->adapter->canUseCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit()
    {
        return $this->adapter->canEdit();
    }

    /**
     * {@inheritdoc}
     */
    public function canFetchTransactionInfo()
    {
        return $this->adapter->canFetchTransactionInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchTransactionInfo(InfoInterface $payment, $transactionId)
    {
        return $this->adapter->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function isGateway()
    {
        return $this->adapter->isGateway();
    }

    /**
     * {@inheritdoc}
     */
    public function isOffline()
    {
        return $this->adapter->isOffline();
    }

    /**
     * {@inheritdoc}
     */
    public function isInitializeNeeded()
    {
        return $this->adapter->isInitializeNeeded();
    }

    /**
     * {@inheritdoc}
     */
    public function canUseForCountry($country)
    {
        return $this->adapter->canUseForCountry($country);
    }

    /**
     * {@inheritdoc}
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->adapter->canUseForCurrency($currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoBlockType()
    {
        return $this->adapter->getInfoBlockType();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoInstance()
    {
        return $this->adapter->getInfoInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function setInfoInstance(InfoInterface $info)
    {
        $this->adapter->setInfoInstance($info);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->adapter->validate();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function order(InfoInterface $payment, $amount)
    {
        $this->adapter->order($payment, $amount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        $this->adapter->authorize($payment, $amount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function capture(InfoInterface $payment, $amount)
    {
        $this->adapter->capture($payment, $amount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function refund(InfoInterface $payment, $amount)
    {
        $this->adapter->refund($payment, $amount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(InfoInterface $payment)
    {
        $this->adapter->cancel($payment);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function void(InfoInterface $payment)
    {
        $this->adapter->void($payment);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function canReviewPayment()
    {
        return $this->adapter->canReviewPayment();
    }

    /**
     * {@inheritdoc}
     */
    public function acceptPayment(InfoInterface $payment)
    {
        $this->adapter->acceptPayment($payment);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function denyPayment(InfoInterface $payment)
    {
        $this->adapter->denyPayment($payment);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->adapter->getConfigData($field, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->adapter->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoUrl()
    {
        $locale = strtolower($this->resolver->getLocale());
        $code = $this->getCode();
        $klarna_code = substr($code, 7);
        if ($klarna_code === 'pay_over_time') {
            $klarna_code = 'slice_it';
        }
        if ($klarna_code === 'direct_debit') {
            $klarna_code = 'pay_now';
        }
        if ($klarna_code === 'direct_bank_transfer') {
            $klarna_code = 'pay_now';
        }
        return sprintf(self::KLARNA_LOGO, $locale, $klarna_code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get translated tag-line text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTagLine()
    {
        $code = $this->getCode();
        $text = 'Klarna Payments';
        switch ($code) {
            case 'klarna_pay_now':
                $text = 'Simple and secure.';
                break;
            case 'klarna_pay_later':
                $text = 'Pay X days after delivery';
                break;
            case 'klarna_pay_over_time':
                $text = 'Pay over time';
                break;
            case 'klarna_direct_debit':
                $text = 'Fast and simple';
                break;
            case 'klarna_direct_bank_transfer':
                $text = 'Simple and secure.';
                break;
        }
        return __($text);
    }

    /**
     * {@inheritdoc}
     */
    public function assignData(DataObject $data)
    {
        $this->adapter->assignData($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $result = $this->adapter->isAvailable($quote);
        if (!$result) {
            return $result;
        }
        return $this->getSessionInitiator()->checkAvailable($quote, $this->getCode());
    }

    /**
     * @return SessionInitiatorInterface
     */
    private function getSessionInitiator()
    {
        return $this->sessionInitiatorFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this->adapter->initialize($paymentAction, $stateObject);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigPaymentAction()
    {
        return $this->adapter->getConfigPaymentAction();
    }
}
