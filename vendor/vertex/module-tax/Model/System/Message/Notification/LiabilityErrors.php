<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\System\Message\Notification;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Vertex\Tax\Model\Config;

/**
 * This class displays notifications in the admin panel about possible tax liability errors.
 *
 * Tax liability errors may be caused by configuration incompatible with Vertex
 */
class LiabilityErrors implements MessageInterface
{
    /** @var Config */
    private $config;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var null|bool */
    private $isVertexEnabledSomewhere;

    /** @var UrlInterface */
    private $urlBuilder;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getIdentity()
    {
        return 'TAX_NOTIFICATION_LIABILITY_ERROR';
    }

    /**
     * @inheritdoc
     */
    public function isDisplayed()
    {
        $currencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        return $currencyCode !== 'USD' && $this->isVertexEnabledSomewhere();
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return '<p>' .
            __('Please change your base currency to US Dollars to ensure tax liability is calculated correctly.') .
            '</p><p>' .
            __(
                'Click here to go to <a href="%1">Currency Setup</a> and change your settings.',
                $this->getManageUrl()
            ) .
            '</p>';
    }

    /**
     * @inheritdoc
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }

    /**
     * Retrieve the URL to access Vertex configuration
     *
     * @return string
     */
    private function getManageUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/currency');
    }

    /**
     * Return whether or not a store has Vertex enabled
     *
     * Loops through all stores, checking to see if one of them has Vertex enabled
     *
     * @return bool
     */
    private function isVertexEnabledSomewhere()
    {
        if ($this->isVertexEnabledSomewhere !== null) {
            return $this->isVertexEnabledSomewhere;
        }

        $stores = $this->storeManager->getStores(true);
        $isActive = false;
        foreach ($stores as $store) {
            $activeTest = $this->config->isVertexActive($store->getId());
            if ($activeTest) {
                $isActive = true;
                break;
            }
        }

        $this->isVertexEnabledSomewhere = $isActive;
        return $isActive;
    }
}
