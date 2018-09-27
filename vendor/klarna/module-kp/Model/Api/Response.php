<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api;

use Klarna\Kp\Api\Data\ResponseInterface;

/**
 * Class Response
 *
 * @package Klarna\Kp\Model\Api
 */
class Response implements ResponseInterface
{
    use Export;

    /**
     * @var string
     */
    private $session_id;

    /**
     * @var string
     */
    private $client_token;

    /**
     * @var int
     */
    private $fraud_status;

    /**
     * @var string
     */
    private $redirect_url;

    /**
     * @var string
     */
    private $order_id;

    /**
     * @var int
     */
    private $response_code = 418;

    /**
     * @var  string
     */
    private $message;

    /**
     * @var array
     */
    private $payment_method_categories = [];

    /**
     * Response constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * @return string
     */
    public function getClientToken()
    {
        return $this->client_token;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     * @return array
     */
    public function getPaymentMethodCategories()
    {
        return $this->payment_method_categories;
    }

    /**
     * @return int
     */
    public function getFraudStatus()
    {
        return $this->fraud_status;
    }

    /**
     * @return bool
     */
    public function isSuccessfull()
    {
        return in_array($this->response_code, [200, 201, 204], false);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
