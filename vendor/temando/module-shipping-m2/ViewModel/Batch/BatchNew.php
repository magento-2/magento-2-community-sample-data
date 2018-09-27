<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Batch;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\BatchProviderInterface;
use Temando\Shipping\ViewModel\CoreApiInterface;
use Temando\Shipping\ViewModel\DataProvider\BatchUrl;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccess;
use Temando\Shipping\ViewModel\DataProvider\CoreApiAccessInterface;
use Temando\Shipping\ViewModel\DataProvider\EntityUrlInterface;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccess;
use Temando\Shipping\ViewModel\DataProvider\ShippingApiAccessInterface;
use Temando\Shipping\ViewModel\ShippingApiInterface;

/**
 * View model for batch list JS component.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchNew implements ArgumentInterface, CoreApiInterface, ShippingApiInterface
{
    /**
     * @var CoreApiAccess
     */
    private $coreApiAccess;

    /**
     * @var ShippingApiAccess
     */
    private $shippingApiAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var BatchUrl
     */
    private $batchUrl;

    /**
     * @var BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * BatchNew constructor.
     * @param CoreApiAccess $coreApiAccess
     * @param ShippingApiAccess $shippingApiAccess
     * @param UrlInterface $urlBuilder
     * @param BatchUrl $batchUrl
     * @param BatchProviderInterface $batchProvider
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $serializer
     */
    public function __construct(
        CoreApiAccess $coreApiAccess,
        ShippingApiAccess $shippingApiAccess,
        UrlInterface $urlBuilder,
        BatchUrl $batchUrl,
        BatchProviderInterface $batchProvider,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Json $serializer
    ) {
        $this->coreApiAccess = $coreApiAccess;
        $this->shippingApiAccess = $shippingApiAccess;
        $this->urlBuilder = $urlBuilder;
        $this->batchUrl = $batchUrl;
        $this->batchProvider = $batchProvider;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return CoreApiAccessInterface
     */
    public function getCoreApiAccess(): CoreApiAccessInterface
    {
        return $this->coreApiAccess;
    }

    /**
     * @return ShippingApiAccessInterface
     */
    public function getShippingApiAccess(): ShippingApiAccessInterface
    {
        return $this->shippingApiAccess;
    }

    /**
     * @return string
     */
    public function getOrderListEndpoint(): string
    {
        $endpoint = $this->urlBuilder->getDirectUrl("rest/V1/orders", ['_secure' => true]);
        return $endpoint;
    }

    /**
     * @return EntityUrlInterface|BatchUrl
     */
    public function getBatchUrl(): EntityUrlInterface
    {
        return $this->batchUrl;
    }

    /**
     * Prepare component init order data.
     *
     * The component only needs the IDs, more details will be fetched via the
     * salesOrderRepositoryV1 endpoint. Only the weight unit is not available
     * there so we pass it right in here.
     *
     * @return string
     */
    public function getOrderData(): string
    {
        $data = [];
        $weightUnits = [];

        $orders = $this->batchProvider->getOrders();
        foreach ($orders as $order) {
            $storeCode = $this->storeManager->getStore($order->getStoreId())->getCode();

            if (!isset($weightUnits[$storeCode])) {
                $weightUnit = $this->scopeConfig->getValue(
                    DirectoryHelper::XML_PATH_WEIGHT_UNIT,
                    ScopeInterface::SCOPE_STORE,
                    $storeCode
                );
                $weightUnits[$storeCode] = $weightUnit;
            }

            $data[$order->getEntityId()] = [];
            $data[$order->getEntityId()]['id'] = $order->getEntityId();
            $data[$order->getEntityId()]['weight_unit'] = $weightUnits[$storeCode];
        }

        return $this->serializer->serialize($data);
    }
}
