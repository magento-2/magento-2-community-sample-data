<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest;

use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Rest\Logger\Anonymizer;
use Temando\Shipping\Test\Integration\Provider\RestResponseProvider;
use Temando\Shipping\Webservice\Logger\LogAnonymizerInterface;

/**
 * LoggerTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Delegate provisioning of test data to separate class
     * @return string[]
     */
    public function logMessageDataProvider()
    {
        return RestResponseProvider::sensitiveDataRequestDataProvider();
    }

    /**
     * @test
     * @dataProvider logMessageDataProvider
     *
     * @param string $logMessage
     */
    public function testCredentialsBeingObfuscated($logMessage)
    {
        $secrets = [
            'accountId' => '[test]',
            'bearerToken' => '[test]',
            'sessionToken' => '[test]',
            'organisationName' => '[test]',
            'email' => '[test]',
            'phoneNumber' => '[test]',
            'personFirstName' => '[test]',
            'personFirstLastName' => '[test]',
            'total' => '[test]',                  // json object
            'lines' => '[test]'                   // json array
        ];

        Bootstrap::getObjectManager()->configure([
            Anonymizer::class => [
                'arguments' => [
                    'secrets' => $secrets
                ]
            ]
        ]);
        /** @var LogAnonymizerInterface $anonymizer */
        $anonymizer = Bootstrap::getObjectManager()->get(LogAnonymizerInterface::class);
        $anonMessage = $anonymizer->anonymize($logMessage);

        foreach ($secrets as $propertyName => $replacement) {
            if (strstr($logMessage, $propertyName) !== false) {
                // assert property names are still there and have their replacement attached
                $this->assertContains("\"$propertyName\": \"$replacement\"", $anonMessage);
            }
        }
    }
}
