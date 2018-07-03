<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice\Logger;

/**
 * Temando Log Message Anonymizer
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface LogAnonymizerInterface
{
    /**
     * Strip any sensitive data from message.
     *
     * @param string $message
     * @return string
     */
    public function anonymize($message);

    /**
     * Processor for Monolog log records.
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    public function __invoke(array $record);
}
