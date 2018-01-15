<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Test\Integration\Provider;

use Magento\Framework\Filesystem\Driver\File as Filesystem;

/**
 * RestResponseProvider
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
final class RestResponseProvider
{
    /**
     * @return string[]
     */
    public static function sensitiveDataRequestDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/sensitiveDataRequest.json')
            ],
            'response_2' => [
                $driver->fileGetContents(__DIR__ . '/_files/updateOrderResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function startSessionResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/startSessionResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function startSessionValidationErrorResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/startSessionValidationError.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function startSessionBadRequestResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/startSessionBadRequestError.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getCarriersResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getCarriersResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getLocationsResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getLocationsResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getContainersResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getContainersResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getShipmentResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getShipmentResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function createOrderResponseProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/createOrderResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function updateOrderResponseProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/updateOrderResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getCompletionsResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getCompletionsResponse.json')
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getCompletionResponseDataProvider()
    {
        $driver = new Filesystem();
        return [
            'response_1' => [
                $driver->fileGetContents(__DIR__ . '/_files/getCompletionResponse.json')
            ]
        ];
    }
}
