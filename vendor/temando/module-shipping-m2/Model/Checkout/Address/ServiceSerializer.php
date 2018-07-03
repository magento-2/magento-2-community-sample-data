<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Address;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeInterfaceFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Temando Checkout Address Service Selection Serializer
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ServiceSerializer implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var AttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * ServiceSerializer constructor.
     * @param SerializerInterface $serializer
     * @param AttributeInterfaceFactory $attributeFactory
     */
    public function __construct(
        SerializerInterface $serializer,
        AttributeInterfaceFactory $attributeFactory
    ) {
        $this->serializer = $serializer;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param AttributeInterface[] $services
     * @return bool|string
     */
    public function serialize($services)
    {
        $data = [];

        if (is_array($services)) {
            foreach ($services as $service) {
                $data[$service->getAttributeCode()] = $service->getValue();
            }
        }

        return $this->serializer->serialize($data);
    }

    /**
     * @param string $string
     * @return AttributeInterface[]
     */
    public function unserialize($string)
    {
        /** @var string[] $data */
        $data = $this->serializer->unserialize($string);
        if (empty($data) || !is_array($data)) {
            return [];
        }

        array_walk($data, function (&$value, $key) {
            /** @var AttributeInterface $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->setAttributeCode($key);
            $attribute->setValue($value);

            // replace plain service value by AttributeInterface
            $value = $attribute;
        });

        /** @var AttributeInterface[] $services */
        $services = $data;
        return $services;
    }
}
