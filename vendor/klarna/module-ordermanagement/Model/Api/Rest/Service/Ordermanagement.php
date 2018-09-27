<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Model\Api\Rest\Service;

use Klarna\Core\Api\ServiceInterface;
use Klarna\Core\Helper\VersionInfo;
use Magento\Framework\DataObject;

/**
 * Class Ordermanagement
 *
 * @package Klarna\Ordermanagement\Model\Api\Rest\Service
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Ordermanagement
{
    const API_VERSION = 'v1';

    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * Initialize class
     *
     * @param ServiceInterface $service
     * @param VersionInfo      $versionInfo
     */
    public function __construct(
        ServiceInterface $service,
        VersionInfo $versionInfo
    ) {
        $this->service = $service;

        $version = sprintf(
            '%s;Core/%s',
            $versionInfo->getVersion('Klarna_Ordermanagement'),
            $versionInfo->getVersion('Klarna_Core')
        );

        $mageMode = $versionInfo->getMageMode();
        $mageVersion = $versionInfo->getMageEdition() . ' ' . $versionInfo->getMageVersion();
        $mageInfo = "Magento {$mageVersion} {$mageMode} mode";
        $this->service->setUserAgent('Magento2_OM', $version, $mageInfo);
        $this->service->setHeader('Accept', '*/*');
    }

    /**
     * Setup connection based on store config
     *
     * @param string $user
     * @param string $password
     * @param string $url
     */
    public function resetForStore($user, $password, $url)
    {
        $this->service->connect($user, $password, $url);
    }

    /**
     * Used by merchants to acknowledge the order.
     *
     * Merchants will receive the order confirmation push until the order has been acknowledged.
     *
     * @param $orderId
     *
     * @return array
     */
    public function acknowledgeOrder($orderId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/acknowledge";
        return $this->service->makeRequest($url, '', ServiceInterface::POST, $orderId);
    }

    /**
     * Get the current state of an order
     *
     * @param $orderId
     *
     * @return array
     */
    public function getOrder($orderId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}";
        return $this->service->makeRequest($url, '', ServiceInterface::GET, $orderId);
    }

    /**
     * Update the total order amount of an order, subject to a new customer credit check.
     *
     * The updated amount can optionally be accompanied by a descriptive text and new order lines. Supplied order lines
     * will replace the existing order lines. If no order lines are supplied in the call, the existing order lines will
     * be deleted. The updated 'order_amount' must not be negative, nor less than current 'captured_amount'. Currency
     * is inferred from the original order.
     *
     * @param string $orderId
     * @param array  $data
     *
     * @return array
     */
    public function updateOrderItems($orderId, $data)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/authorization";
        return $this->service->makeRequest($url, $data, ServiceInterface::PATCH, $orderId);
    }

    /**
     * Extend the order's authorization by default period according to merchant contract.
     *
     * @param string $orderId
     *
     * @return array
     */
    public function extendAuthorization($orderId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/extend-authorization-time";
        return $this->service->makeRequest($url, '', ServiceInterface::POST, $orderId);
    }

    /**
     * Update one or both merchant references. To clear a reference, set its value to "" (empty string).
     *
     * @param string $orderId
     * @param string $merchantReference1
     * @param string $merchantReference2
     *
     * @return array
     */
    public function updateMerchantReferences($orderId, $merchantReference1, $merchantReference2 = null)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/merchant-references";

        $data = [
            'merchant_reference1' => $merchantReference1
        ];

        if ($merchantReference2 !== null) {
            $data['merchant_reference2'] = $merchantReference2;
        }
        return $this->service->makeRequest($url, $data, ServiceInterface::PATCH, $orderId);
    }

    /**
     * Update billing and/or shipping address for an order, subject to customer credit check.
     * Fields can be updated independently. To clear a field, set its value to "" (empty string).
     *
     * Mandatory fields can not be cleared
     *
     * @param string $orderId
     * @param array  $data
     *
     * @return array
     */
    public function updateAddresses($orderId, $data)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/customer-details";
        return $this->service->makeRequest($url, $data, ServiceInterface::PATCH, $orderId);
    }

    /**
     * Add shipping info to capture
     *
     * @param string $orderId
     * @param string $captureId
     * @param array $data
     * @return array
     */
    public function addShippingInfo($orderId, $captureId, $data)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures/{$captureId}/shipping-info";
        return $this->service->makeRequest($url, $data, ServiceInterface::POST, $orderId);
    }

    /**
     * Cancel an authorized order. For a cancellation to be successful, there must be no captures on the order.
     * The authorized amount will be released and no further updates to the order will be allowed.
     *
     * @param $orderId
     *
     * @return array
     */
    public function cancelOrder($orderId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/cancel";
        return $this->service->makeRequest($url, '', ServiceInterface::POST, $orderId);
    }

    /**
     * Capture the supplied amount. Use this call when fulfillment is completed, e.g. physical goods are being shipped
     * to the customer.
     * 'captured_amount' must be equal to or less than the order's 'remaining_authorized_amount'.
     * Shipping address is inherited from the order. Use PATCH method below to update the shipping address of an
     * individual capture. The capture amount can optionally be accompanied by a descriptive text and order lines for
     * the captured items.
     *
     * @param $orderId
     * @param $data
     *
     * @return array
     * @throws \Klarna\Core\Model\Api\Exception
     */
    public function captureOrder($orderId, $data)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures";
        return $this->service->makeRequest($url, $data, ServiceInterface::POST, $orderId);
    }

    /**
     * Retrieve a capture
     *
     * @param $orderId
     * @param $captureId
     *
     * @return array
     */
    public function getCapture($orderId, $captureId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures/{$captureId}";
        return $this->service->makeRequest($url, '', ServiceInterface::GET, $orderId);
    }

    /**
     * Appends new shipping info to a capture.
     *
     * @param $orderId
     * @param $captureId
     * @param $data
     *
     * @return array
     */
    public function addShippingDetailsToCapture($orderId, $captureId, $data)
    {
        // @codingStandardsIgnoreLine
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures/{$captureId}/shipping-info";
        return $this->service->makeRequest($url, $data, ServiceInterface::POST, $orderId);
    }

    /**
     * Update the billing address for a capture. Shipping address can not be updated.
     * Fields can be updated independently. To clear a field, set its value to "" (empty string).
     *
     * Mandatory fields can not be cleared,
     *
     * @param $orderId
     * @param $captureId
     * @param $data
     *
     * @return array
     */
    public function updateCaptureBillingAddress($orderId, $captureId, $data)
    {
        // @codingStandardsIgnoreLine
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures/{$captureId}/customer-details";
        return $this->service->makeRequest($url, $data, ServiceInterface::PATCH, $orderId);
    }

    /**
     * Trigger a new send out of customer communication., typically a new invoice, for a capture.
     *
     * @param $orderId
     * @param $captureId
     *
     * @return array
     */
    public function resendOrderInvoice($orderId, $captureId)
    {
        // @codingStandardsIgnoreLine
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/captures/{$captureId}/trigger-send-out";
        return $this->service->makeRequest($url, '', ServiceInterface::POST, $orderId);
    }

    /**
     * Refund an amount of a captured order. The refunded amount will be credited to the customer.
     * The refunded amount must not be higher than 'captured_amount'.
     * The refunded amount can optionally be accompanied by a descriptive text and order lines.
     *
     * @param $orderId
     * @param $data
     *
     * @return array
     */
    public function refund($orderId, $data)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/refunds";
        return $this->service->makeRequest($url, $data, ServiceInterface::POST, $orderId);
    }

    /**
     * Signal that there is no intention to perform further captures.
     *
     * @param $orderId
     *
     * @return array
     */
    public function releaseAuthorization($orderId)
    {
        $url = "/ordermanagement/" . self::API_VERSION . "/orders/{$orderId}/release-remaining-authorization";
        return $this->service->makeRequest($url, '', ServiceInterface::POST, $orderId);
    }

    /**
     * Get resource id from Location URL
     *
     * This assumes the ID is the last url path
     *
     * @param string|array|DataObject $location
     *
     * @return string
     */
    public function getLocationResourceId($location)
    {
        if ($location instanceof DataObject) {
            $responseObject = $location->getResponseObject();
            $location = $responseObject['headers']['Location'];
        }
        if (is_array($location)) {
            $location = array_shift($location);
        }

        $location = rtrim($location, '/');
        $locationArr = explode('/', $location);
        return array_pop($locationArr);
    }
}
