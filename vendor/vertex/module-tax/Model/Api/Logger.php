<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Api\Utility\SoapClientRegistry;
use Vertex\Tax\Model\RequestLogger;

/**
 * Contains functionality for logging API calls
 */
class Logger
{
    /** @var LoggerInterface */
    private $logger;

    /** @var RequestLogger */
    private $requestLogger;

    /** @var SoapClientRegistry */
    private $soapClientRegistry;

    /**
     * @param LoggerInterface $logger
     * @param RequestLogger $requestLogger
     * @param SoapClientRegistry $soapClientRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        RequestLogger $requestLogger,
        SoapClientRegistry $soapClientRegistry
    ) {
        $this->logger = $logger;
        $this->requestLogger = $requestLogger;
        $this->soapClientRegistry = $soapClientRegistry;
    }

    /**
     * Wrap an API call to ensure it is logged
     *
     * @param callable $callable
     * @param string $type
     * @return mixed Result of callable
     * @throws \Exception
     */
    public function wrapCall(callable $callable, $type)
    {
        try {
            return $callable();
        } catch (\Exception $exception) {
            $this->logException($exception);
            throw $exception;
        } finally {
            $this->logRequest($type);
        }
    }

    /**
     * Log an Exception
     *
     * @param \Exception $exception
     * @return void
     */
    private function logException(\Exception $exception)
    {
        $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
    }

    /**
     * Log an API call to the database
     *
     * @param string $requestType
     * @return void
     */
    private function logRequest($requestType)
    {
        $soapClient = $this->soapClientRegistry->getLastClient();
        try {
            $this->requestLogger->log(
                $requestType,
                $soapClient ? $soapClient->__getLastRequest() : null,
                $soapClient ? $soapClient->__getLastResponse() : null
            );
        } catch (CouldNotSaveException $originalException) {
            $loggedException = new \Exception('Failed to log Vertex Request', 0, $originalException);
            $this->logException($loggedException);
        }
    }
}
