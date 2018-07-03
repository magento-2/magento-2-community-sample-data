<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Checkout\Schema;

/**
 * CheckoutField
 *
 * Typed representation of the original json field definition.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CheckoutField
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $orderPath;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * CheckoutField constructor.
     * @param string $id
     * @param string $label
     * @param string $type
     * @param string $orderPath
     * @param string $defaultValue
     * @param mixed[] $options
     */
    public function __construct(
        $id,
        $label,
        $type,
        $orderPath,
        $defaultValue = '',
        array $options = []
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->orderPath = $orderPath;
        $this->defaultValue = $defaultValue;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getOrderPath()
    {
        return $this->orderPath;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
