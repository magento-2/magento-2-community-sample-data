<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Schema;

use Magento\Framework\Serialize\Serializer\Json;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * CheckoutFields
 *
 * Provide the checkout fields schema in an unserialized, determined format.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutFieldsSchema
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * @var CheckoutFieldFactory
     */
    private $fieldFactory;

    /**
     * @var Json
     */
    private $decoder;

    /**
     * CheckoutFieldsDefinition constructor.
     * @param ModuleConfigInterface $moduleConfig
     * @param CheckoutFieldFactory $fieldFactory
     * @param Json $decoder
     */
    public function __construct(
        ModuleConfigInterface $moduleConfig,
        CheckoutFieldFactory $fieldFactory,
        Json $decoder
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->fieldFactory = $fieldFactory;
        $this->decoder = $decoder;
    }

    /**
     * @param string $type
     * @return string
     */
    private function mapFieldType($type)
    {
        $fieldTypeMap = [
            'inputText' => 'text',
            'inputNumber' => 'number',
        ];

        if (isset($fieldTypeMap[$type])) {
            return $fieldTypeMap[$type];
        }

        return $type;
    }

    /**
     * @return CheckoutField[]
     */
    public function getFields()
    {
        $fields = [];

        try {
            $fieldDefinitions = $this->decoder->unserialize($this->moduleConfig->getCheckoutFieldsDefinition());
        } catch (\InvalidArgumentException $e) {
            $fieldDefinitions = [];
        }

        foreach ($fieldDefinitions as $fieldDefinition) {
            $fieldData = [
                'id' => $fieldDefinition['id'],
                'label' => $fieldDefinition['label'],
                'type' => $this->mapFieldType($fieldDefinition['fieldType']),
                'orderPath' => $fieldDefinition['orderPath'],
            ];

            if (isset($fieldDefinition['defaultValue'])) {
                $fieldData['value'] = $fieldDefinition['defaultValue'];
                $fieldData['defaultValue'] = $fieldDefinition['defaultValue'];
            }

            if (isset($fieldDefinition['options'])) {
                $fieldData['options'] = $fieldDefinition['options'];
            }

            $field = $this->fieldFactory->create($fieldData);
            $fields[$field->getId()] = $field;
        }

        return $fields;
    }
}
