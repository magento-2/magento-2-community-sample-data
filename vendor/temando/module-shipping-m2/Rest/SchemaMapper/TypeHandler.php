<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\SchemaMapper;

use Magento\Framework\ObjectManagerInterface;
use Temando\Shipping\Rest\SchemaMapper\Reflection\AbstractTypeHandler;
use Temando\Shipping\Rest\SchemaMapper\Reflection\ReflectionInterface;

/**
 * Temando API Type Instantiator Utility
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TypeHandler extends AbstractTypeHandler
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * TypeHandler constructor.
     * @param ReflectionInterface $reflect
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ReflectionInterface $reflect, ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;

        parent::__construct($reflect);
    }

    /**
     * Generic API type factory method.
     *
     * @param string $type
     * @return mixed
     */
    public function create($type)
    {
        $type = preg_replace('/\[\]$/', '', $type);
        return $this->objectManager->create($type);
    }
}
