<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\System\Message\Notification;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Model\Config;

/**
 * This class displays notifications in the admin panel when catalog prices are set to include tax
 *
 * Due to performance concerns, Vertex does not support displaying taxes in catalog prices at this time.  This
 * notification is used to inform the admin user when Vertex is automatically disabled when such a setting is turned on.
 */
class DisplayTaxesInCatalog implements MessageInterface
{
    /** @var Config */
    private $config;

    /** @var LoggerInterface */
    private $logger;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var WebsiteRepositoryInterface */
    private $websiteRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return 'VERTEX_NOTIFICATION_TAX_IN_CATALOG';
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $storesAffected = $this->getStoresAffected();

        return '<p>'
            . __(
                'Vertex Tax Calculation has been automatically disabled. ' .
                'Display prices in Catalog must be set to "Excluding Tax" to use Vertex.'
            )
            . '</p><p>'
            . ($storesAffected > 1 ? __('Stores affected: ') : __('Store affected: '))
            . implode(', ', $storesAffected)
            . '</p><p>'
            . __(
                'Click here to go to <a href="%1">Price Display Settings</a> and change your settings.',
                $this->getManageUrl()
            )
            . '</p>';
    }

    /**
     * Check whether or not to display the error message
     *
     * @return bool
     */
    public function isDisplayed()
    {
        $stores = $this->getStoresAffected();
        return count($stores) > 0;
    }

    /**
     * Retrieve the URL for modifying the configuration that has caused this
     *
     * @return string
     */
    private function getManageUrl()
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            ['section' => 'tax', '_fragment' => 'tax_display-head']
        );
    }

    /**
     * Retrieve a list of stores where these incompatible settings exist
     *
     * @return string[]
     */
    private function getStoresAffected()
    {
        $affectedStores = [];

        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            if ($this->isStoreAffected($store->getId())) {
                try {
                    $websiteName = $this->websiteRepository->getById($store->getWebsiteId())->getName();
                } catch (\Exception $e) {
                    // This shouldn't happen :|
                    $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                    $websiteName = 'Unknown Website';
                }
                $affectedStores[] = $websiteName . ' (' . $store->getName() . ')';
            }
        }

        return $affectedStores;
    }

    /**
     * Determine whether or not a store has incompatible settings
     *
     * @param string|null $store
     * @return bool
     */
    private function isStoreAffected($store = null)
    {
        return $this->config->isVertexActive($store)
            && $this->config->isDisplayPriceInCatalogEnabled($store);
    }
}
