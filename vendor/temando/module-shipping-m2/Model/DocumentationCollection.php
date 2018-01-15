<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * Temando Documentation Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DocumentationCollection extends \ArrayIterator
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * DocumentationCollection constructor.
     *
     * @param Json $serializer
     * @param \Temando\Shipping\Model\DocumentationInterface[] $docs
     * @param int $flags
     */
    public function __construct(
        Json $serializer,
        array $docs = [],
        $flags = 0
    ) {
        $this->serializer = $serializer;

        parent::__construct($docs, $flags);
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
     * @return string
     */
    public function serialize()
    {
        return $this->serializer->serialize($this);
    }
}
