<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Trim config value
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class TrimmedValue extends Value
{
    /**
     * Trim value before save
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $this->setValue(trim($value));

        return parent::beforeSave();
    }
}
