<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Cache;

/**
 * Handle data exchange serialization for StorageInterface.
 *
 * This is a compatibility fill for unsupported SerializerInterface in Magento < 2.2.
 */
class Serializer
{
    const MAX_ARRAY_DEPTH = 255;

    /**
     * Declaration of accepted types for serialization.
     *
     * @var array
     */
    private $supportedTypes = [
        'string',
        'integer',
        'double',
        'boolean',
        'NULL',
        'array',
        'object',
    ];

    /**
     * {@inheritdoc}
     */
    public function serialize($data)
    {
        $this->validate($data);

        return \json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string)
    {
        // Resets last error state
        \json_decode('{}');

        $result = \json_decode($string, false);

        if (\json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Cannot unserialize input');
        }

        return $result;
    }

    /**
     * Perform input validation to ensure that only supported data types are accepted for serialization.
     *
     * @param mixed $input
     * @param int $depth
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validate($input, $depth = 0)
    {
        if (!in_array(gettype($input), $this->supportedTypes)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot serialize unsupported type "%s"', gettype($input))
            );
        } elseif (gettype($input) === 'object' && !($input instanceof \stdClass)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot serialize unsupported object type "%s"', get_class($input))
            );
        } elseif (gettype($input) === 'array') {
            $depth++;

            if ($depth > self::MAX_ARRAY_DEPTH) {
                throw new \InvalidArgumentException(
                    sprintf('Serializable array depth cannot exceed %d', self::MAX_ARRAY_DEPTH)
                );
            }

            foreach ($input as $item) {
                $this->validate($item, $depth);
            }
        }
    }
}
