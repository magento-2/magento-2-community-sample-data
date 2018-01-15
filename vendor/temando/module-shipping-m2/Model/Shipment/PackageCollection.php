<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Shipment;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * Temando Package Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PackageCollection extends \ArrayIterator
{
    /**
     * @var Json;
     */
    private $serializer;

    /**
     * PackageCollection constructor
     *
     * @param Json $serializer
     * @param \Temando\Shipping\Model\Shipment\PackageInterface[] $packages
     * @param int $flags
     */
    public function __construct(
        Json $serializer,
        array $packages = [],
        $flags = 0
    ) {
        $this->serializer = $serializer;

        parent::__construct($packages, $flags);
    }

    /**
     * Unserialize
     *
     * @param string $serialized
     *
     * @return mixed
     */
    public function unserialize($serialized)
    {
        return $this->serializer->unserialize($serialized);
    }

    /**
     * Serialize
     *
     * @return string The serialized
     */
    public function serialize()
    {
        return $this->serializer->serialize($this);
    }
}
