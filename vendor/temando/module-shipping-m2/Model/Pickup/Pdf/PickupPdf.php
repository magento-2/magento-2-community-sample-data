<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Pickup\Pdf;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Pdf\AbstractPdf;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\Items\AbstractItems;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Location\OrderAddressFactory;
use Temando\Shipping\Model\Pickup\Pdf\PickupItemRendererFactory;
use Temando\Shipping\Model\Pickup\PickupManagementFactory;
use Temando\Shipping\Model\PickupInterface;

/**
 * Temando Pickup Order PDF model
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class PickupPdf extends AbstractPdf
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var PickupItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var PickupManagementFactory
     */
    private $pickupManagementFactory;

    /**
     * @var OrderAddressFactory
     */
    private $orderAddressFactory;

    /**
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param PickupItemRendererFactory $itemRendererFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param PickupManagementFactory $pickupManagementFactory
     * @param OrderAddressFactory $orderAddressFactory
     * @param array $data
     *
     */
    public function __construct(
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        PickupItemRendererFactory $itemRendererFactory,
        DataObjectFactory $dataObjectFactory,
        PickupManagementFactory $pickupManagementFactory,
        OrderAddressFactory $orderAddressFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->pickupManagementFactory = $pickupManagementFactory;
        $this->orderAddressFactory = $orderAddressFactory;

        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }

    /**
     * @return DataObject
     */
    private function getPickupDetails()
    {
        /** @var PickupInterface $pickup */
        $pickup = $this->getData('pickup');
        /** @var OrderInterface | Order $order */
        $order = $this->getData('order');

        $pickupLocation = $pickup->getPickupLocation();
        /** @var \Magento\Sales\Model\Order\Address $deliveryAddress */
        $deliveryAddress = $this->orderAddressFactory->createFromShipmentLocation($pickupLocation);
        $deliveryAddress->unsetData('firstname');
        $deliveryAddress->setData('lastname', $pickupLocation->getName());
        $deliveryAddress->setParentId($order->getEntityId());
        $order->setData('addresses', [$order->getBillingAddress(), $deliveryAddress]);

        $pickupItems = $pickup->getItems();
        /** @var Item[] $orderItems */
        $orderItems = $order->getItems();
        $pickupManagement = $this->pickupManagementFactory->create(['pickups' => $this->getData('pickups')]);
        if ($pickupManagement->canPrepare($pickup->getPickupId())) {
            $pickupItems = $pickupManagement->getOpenItems($orderItems);
        }
        $items = [];
        foreach ($pickupItems as $sku => $qty) {
            foreach ($orderItems as $key => $orderItem) {
                if ($orderItem->getData('sku') != $sku) {
                    continue;
                }
                $itemData['data'] = [
                    'name' => $orderItem->getData('name'),
                    'qty' => $qty,
                    'sku' => $sku,
                    'order_item' => $orderItem,
                ];
                $items[] = $this->dataObjectFactory->create($itemData);
                unset($orderItems[$key]);
                break;
            }
        }

        $pickupDetails = [
            'store_id' => $order->getStoreId(),
            'order' => $order,
            'store' => $order->getStore(),
            'items' => $items,
            'pickup_id' => $pickup->getPickupId(),
            'increment_id' => $order->getIncrementId(),
        ];

        return $this->dataObjectFactory->create(['data' => $pickupDetails]);
    }

    /**
     * Draw table header for product items
     *
     * @param  \Zend_Pdf_Page $page
     *
     * @return void
     * @throws LocalizedException
     */
    private function drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 100];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 10];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Retrieve renderer model
     *
     * @param  string $type
     *
     * @return AbstractItems
     * @throws LocalizedException
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We found an invalid renderer model.'));
        }

        if ($this->_renderers[$type]['renderer'] === null) {
            $this->_renderers[$type]['renderer'] = $this->itemRendererFactory->create();
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Return PDF document
     *
     * @param DataObject $pickupData
     *
     * @return \Zend_Pdf
     * @throws LocalizedException
     * @throws \Zend_Pdf_Exception
     */
    public function getPdf()
    {
        $pickupData = $this->getPickupDetails();
        $this->_beforeGetPdf();
        $this->_initRenderer('pickup');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        if ($pickupData->getData('store_id')) {
            $this->localeResolver->emulate($pickupData->getData('store_id'));
            $this->storeManager->setCurrentStore($pickupData->getData('store_id'));
        }
        $page = $this->newPage();
        $order = $pickupData->getData('order');
        /* Add image */
        $this->insertLogo($page, $pickupData->getData('store'));
        /* Add address */
        $this->insertAddress($page, $pickupData->getData('store'));
        /* Add head */
        $this->insertOrder(
            $page,
            $pickupData->getData('order'),
            $this->_scopeConfig->isSetFlag(
                self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );
        /* Add document text and number */
        $this->insertDocumentNumber($page, __('Pickup # ') . $pickupData->getData('pickup_id'));
        /* Add table */
        $this->drawHeader($page);
        /* Add body */
        /** @var DataObject $item */
        foreach ($pickupData->getData('items') as $item) {
            if ($item->getData('order_item')->getParentItem()) {
                continue;
            }
            /* Draw item */
            $this->_drawItem($item, $page, $order);
            $page = end($pdf->pages);
        }
        $this->_afterGetPdf();
        if ($pickupData->getData('store_id')) {
            $this->localeResolver->revert();
        }

        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     *
     * @return \Zend_Pdf_Page
     * @throws LocalizedException
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->drawHeader($page);
        }

        return $page;
    }
}
