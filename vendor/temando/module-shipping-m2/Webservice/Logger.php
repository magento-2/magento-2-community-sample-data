<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Webservice;

use Magento\Framework\Logger\Monolog;
use Temando\Shipping\Rest\Exception\RestClientErrorException;
use Temando\Shipping\Webservice\Config\WsConfigInterface;
use Temando\Shipping\Webservice\Exception\HttpResponseException;

/**
 * Webservice communication logger
 *
 * @package  Temando\Shipping\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Logger extends Monolog
{
    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * Logger constructor.
     * @param string $name
     * @param WsConfigInterface $config
     * @param \Monolog\Handler\HandlerInterface[] $handlers
     * @param callable[] $processors
     */
    public function __construct(
        $name,
        WsConfigInterface $config,
        array $handlers = [],
        array $processors = []
    ) {
        $this->config = $config;

        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param string $message
     * @param mixed[] $context
     * @return string
     */
    private function addMessageContext($message, array $context = [])
    {
        if (isset($context['exception']) && ($context['exception'] instanceof RestClientErrorException)) {
            /** @var RestClientErrorException $exception */
            $exception = $context['exception'];
            $previous = $exception->getPrevious();
            if ($previous instanceof HttpResponseException) {
                $message = sprintf("%s\n%s", $previous->getResponseHeaders(), $message);
            }
        }

        return $message;
    }

    /**
     * Log message if logging is enabled via module config.
     * Disregard module config in error cases.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function log($level, $message, array $context = [])
    {
        $monologLevel = parent::toMonologLevel($level);
        if ($this->config->isLoggingEnabled() || $monologLevel >= Monolog::ERROR) {
            $message = $this->addMessageContext($message, $context);
            return parent::log($level, $message, $context);
        }

        return false;
    }
}
