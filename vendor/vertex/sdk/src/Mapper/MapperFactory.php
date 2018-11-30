<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

/**
 * Retrieve an instance of a mapper
 *
 * @api
 */
class MapperFactory
{
    /**
     * List of api-specific mapping implementations for given interfaces
     *
     * Keys in the outer array should be the interface that would be mapped.  Their value should be an associative array
     * where the key is the API level and the value is a mapper valid for that API level
     *
     * @var array
     */
    private $config;

    /**
     * List of object instances
     *
     * Keys in the array are the class name with the value being the instance
     *
     * @var array
     */
    private $instances;

    /**
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        if ($config === null) {
            $config = [
                'Vertex\Mapper\AuthenticatorInterface' => [
                    '60' => 'Vertex\Mapper\Api60\Authenticator',
                    '70' => 'Vertex\Mapper\Api60\Authenticator',
                ],
                'Vertex\Data\AddressInterface' => [
                    '60' => 'Vertex\Mapper\Api60\AddressMapper',
                    '70' => 'Vertex\Mapper\Api60\AddressMapper',
                ],
                'Vertex\Data\CustomerInterface' => [
                    '60' => 'Vertex\Mapper\Api60\CustomerMapper',
                    '70' => 'Vertex\Mapper\Api60\CustomerMapper',
                ],
                'Vertex\Data\JurisdictionInterface' => [
                    '60' => 'Vertex\Mapper\Api60\JurisdictionMapper',
                    '70' => 'Vertex\Mapper\Api60\JurisdictionMapper',
                ],
                'Vertex\Data\LineItemInterface' => [
                    '60' => 'Vertex\Mapper\Api60\LineItemMapper',
                    '70' => 'Vertex\Mapper\Api60\LineItemMapper',
                ],
                'Vertex\Data\LoginInterface' => [
                    '60' => 'Vertex\Mapper\Api60\LoginMapper',
                    '70' => 'Vertex\Mapper\Api60\LoginMapper',
                ],
                'Vertex\Data\SellerInterface' => [
                    '60' => 'Vertex\Mapper\Api60\SellerMapper',
                    '70' => 'Vertex\Mapper\Api60\SellerMapper',
                ],
                'Vertex\Data\TaxAreaLookupResultInterface' => [
                    '60' => 'Vertex\Mapper\Api60\TaxAreaLookupResultMapper',
                    '70' => 'Vertex\Mapper\Api60\TaxAreaLookupResultMapper',
                ],
                'Vertex\Data\TaxInterface' => [
                    '60' => 'Vertex\Mapper\Api60\TaxMapper',
                    '70' => 'Vertex\Mapper\Api60\TaxMapper',
                ],
                'Vertex\Services\Invoice\RequestInterface' => [
                    '60' => 'Vertex\Mapper\Api60\InvoiceRequestMapper',
                    '70' => 'Vertex\Mapper\Api60\InvoiceRequestMapper',
                ],
                'Vertex\Services\Invoice\ResponseInterface' => [
                    '60' => 'Vertex\Mapper\Api60\InvoiceResponseMapper',
                    '70' => 'Vertex\Mapper\Api60\InvoiceResponseMapper',
                ],
                'Vertex\Services\TaxAreaLookup\RequestInterface' => [
                    '60' => 'Vertex\Mapper\Api60\TaxAreaLookupRequestMapper',
                    '70' => 'Vertex\Mapper\Api60\TaxAreaLookupRequestMapper',
                ],
                'Vertex\Services\TaxAreaLookup\ResponseInterface' => [
                    '60' => 'Vertex\Mapper\Api60\TaxAreaLookupResponseMapper',
                    '70' => 'Vertex\Mapper\Api60\TaxAreaLookupResponseMapper',
                ],
                'Vertex\Services\Quote\RequestInterface' => [
                    '60' => 'Vertex\Mapper\Api60\QuoteRequestMapper',
                    '70' => 'Vertex\Mapper\Api60\QuoteRequestMapper',
                ],
                'Vertex\Services\Quote\ResponseInterface' => [
                    '60' => 'Vertex\Mapper\Api60\QuoteResponseMapper',
                    '70' => 'Vertex\Mapper\Api60\QuoteResponseMapper',
                ],
            ];
        }
        $this->config = $config;
    }

    /**
     * Create an instance of an API-specific implementation of a mapper for the given interface
     *
     * @param string $class An interface you want an API-specific mapper for
     * @param string $apiLevel The API level you need the mapper for
     * @return mixed
     */
    public function createForClass($class, $apiLevel)
    {
        $className = $this->getClassName($class, $apiLevel);
        return new $className;
    }

    /**
     * Retrieve the API-specific implementation of a mapper for the given interface
     *
     * @param string $class An interface you want an API-specific mapper for
     * @param string $apiLevel The API level you need the mapper for
     * @return mixed
     */
    public function getForClass($class, $apiLevel)
    {
        $className = $this->getClassName($class, $apiLevel);
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }
        $instance = $this->createForClass($class, $apiLevel);
        $this->instances[$className] = $instance;
        return $instance;
    }

    /**
     * Retrieve the class name of the API-specific implementation of a mapper for the given interface
     *
     * @param string $class
     * @param string $apiLevel
     * @return string
     */
    private function getClassName($class, $apiLevel)
    {
        if (!isset($this->config[$class])) {
            throw new \InvalidArgumentException('No configured mappers for ' . $class);
        }
        if (!isset($this->config[$class][$apiLevel])) {
            throw new \InvalidArgumentException('No configured mapper for ' . $class . ' in API Level ' . $apiLevel);
        }

        return $this->config[$class][$apiLevel];
    }
}
