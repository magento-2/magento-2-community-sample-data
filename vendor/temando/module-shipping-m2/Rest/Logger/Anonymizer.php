<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Logger;

use Temando\Shipping\Webservice\Logger\LogAnonymizerInterface;

/**
 * Temando Log Message Anonymizer
 *
 * Search JSON formatted messages for certain properties and replace their values.
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Anonymizer implements LogAnonymizerInterface
{
    /**
     * @var array | string[]
     */
    private $secrets;

    /**
     * Anonymizer constructor.
     * @param string[] $secrets
     */
    public function __construct($secrets = [])
    {
        $this->secrets = $secrets;
    }

    /**
     * Strip sensitive strings from message by given property names.
     *
     * @param string $message
     * @return string
     */
    public function anonymize($message)
    {
        $patternTemplate = <<<'REGEX'
/(?(DEFINE)
(?<object>(?>\{\s*(?>(?&pair)(?>\s*,\s*(?&pair))*)?\s*\}))
(?<pair>(?>(?&STRING)\s*:\s*(?&value)))
(?<array>(?>\[\s*(?>(?&value)(?>\s*,\s*(?&value))*)?\s*\]))
(?<value>(?>true|false|null|(?&STRING)|(?&NUMBER)|(?&object)|(?&array)))
(?<STRING>(?>"(?>\\\\(?>["\\\\\/bfnrt]|u[a-fA-F0-9]{4})|[^"\\\\\0-\x1F\x7F]+)*"))
(?<NUMBER>(?>-?(?>0|[1-9][0-9]*)(?>\.[0-9]+)?(?>[eE][+-]?[0-9]+)?))
)
(?<key>\"%s\":\s*)(?<replace>(?&value))/xu
REGEX;

        foreach ($this->secrets as $key => $newValue) {
            $pattern = sprintf($patternTemplate, $key);
            $message = preg_replace_callback(
                $pattern,
                function ($matches) use ($newValue) {
                    return $matches['key'] . "\"$newValue\"";
                },
                $message
            );
        }

        return $message;
    }

    /**
     * Processor for Monolog log records.
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    public function __invoke(array $record)
    {
        $record['message'] = $this->anonymize($record['message']);

        return $record;
    }
}
