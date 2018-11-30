<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Pickup\Email\Container;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Store\Model\ScopeInterface;

/**
 * Temando Pickup Identity Container
 *
 * Set the current context before you try to send a mail, to tell the sender which order action for
 * pickup orders is sending a notification
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class PickupIdentity extends OrderIdentity
{
    const XML_PATH_EMAIL_ENABLED = 'sales_email/temando_pickup/enabled';
    const XML_PATH_EMAIL_COPY_TO = 'sales_email/temando_pickup/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/temando_pickup/identity';
    const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/temando_pickup/copy_method';

    /**
     * @var string
     */
    private $templatePath;

    /**
     * Is email enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return list of copy_to emails
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }

    /**
     * Return email copy method
     *
     * @return string
     */
    public function getCopyMethod()
    {
        return (string)$this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return string
     */
    public function getTemplateId()
    {
        return (string)$this->getConfigValue($this->templatePath, $this->getStore()->getStoreId());
    }

    /**
     * Return guest template id
     *
     * @return string
     */
    public function getGuestTemplateId()
    {
        return $this->getTemplateId();
    }

    /**
     * Return email identity
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }

    /**
     * @param string $path
     * @return void
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
    }
}
