<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\System\Config\Source;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Base
 *
 * @package Klarna\Core\Model\System\Config\Source
 */
class Base implements ArrayInterface
{
    /**
     * @var DataInterface
     */
    private $config;
    /**
     * Determines which config option to pull from
     *
     * @var string
     */
    private $optionName = '';

    /**
     * Base constructor.
     *
     * @param DataInterface $config
     * @param string        $optionName
     */
    public function __construct(DataInterface $config, $optionName = '')
    {
        $this->config = $config;
        $this->optionName = $optionName;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $values = $this->config->get($this->optionName);

        if ($values) {
            foreach ($values as $name => $value) {
                $options[] = [
                    'label' => $value['label'],
                    'value' => $name
                ];
            }
        }

        return $options;
    }

    /**
     * @return string
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * @param string $optionName
     * @return $this
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;
        return $this;
    }
}
