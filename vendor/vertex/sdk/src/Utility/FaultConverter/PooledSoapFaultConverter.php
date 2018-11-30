<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility\FaultConverter;

use Vertex\Utility\SoapFaultConverterInterface;

/**
 * Responsible for converting SoapFaults encountered while communicating with Vertex to reasonable Exceptions
 *
 * @api
 */
class PooledSoapFaultConverter implements SoapFaultConverterInterface
{
    /** @var SoapFaultConverterInterface[] */
    private $converters;

    /**
     * @param SoapFaultConverterInterface[] $converters
     * @throws \InvalidArgumentException
     */
    public function __construct(array $converters = [])
    {
        foreach ($converters as $converter) {
            if (!($converter instanceof SoapFaultConverterInterface)) {
                throw new \InvalidArgumentException(
                    'Invalid object given.  All converters must implement SoapFaultConverterInterface.'
                );
            }
        }
        $this->converters = $converters;
    }

    /**
     * @inheritdoc
     */
    public function convert(\SoapFault $fault)
    {
        foreach ($this->converters as $converter) {
            $result = $converter->convert($fault);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
}
