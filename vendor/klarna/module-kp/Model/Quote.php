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

use Klarna\Kp\Api\QuoteInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Quote
 *
 * @package Klarna\Core\Model
 * @method getId():int
 * @method getKlarnaCheckoutId():int
 */
class Quote extends AbstractModel implements QuoteInterface, IdentityInterface
{
    const CACHE_TAG = 'klarna_payments_quote';

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return $this->_getData('session_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($active)
    {
        $this->setData('is_active', $active);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientToken($token)
    {
        $this->setData('client_token', $token);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientToken()
    {
        return $this->_getData('client_token');
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionId($sessionId)
    {
        $this->setData('session_id', $sessionId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return (bool)$this->_getData('is_active');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationToken()
    {
        return $this->_getData('authorization_token');
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorizationToken($token)
    {
        $this->setData('authorization_token', $token);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($quoteId)
    {
        $this->setData('quote_id', $quoteId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteId()
    {
        return $this->_getData('quote_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods()
    {
        $methods = $this->_getData('payment_methods');
        if (empty($methods)) {
            return [];
        }
        return explode(',', $methods);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethods($methods)
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }
        $this->setData('payment_methods', implode(',', $methods));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethodInfo($methodinfo)
    {
        if (!is_array($methodinfo)) {
            $methodinfo = [$methodinfo];
        }
        $this->setData('payment_method_info', json_encode($methodinfo));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodInfo()
    {
        $methods = $this->_getData('payment_method_info');
        if (empty($methods)) {
            return [];
        }
        return json_decode($methods);
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(\Klarna\Kp\Model\ResourceModel\Quote::class);
    }
}
