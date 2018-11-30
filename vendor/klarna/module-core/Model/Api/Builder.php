<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Model\Api;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Model\Checkout\Orderline\Collector;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Data\Address as CustomerAddress;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Url;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Base class to generate API configuration
 *
 * @method Builder setShippingUnitPrice($integer)
 * @method int getShippingUnitPrice()
 * @method Builder setShippingTaxRate($integer)
 * @method int getShippingTaxRate()
 * @method Builder setShippingTotalAmount($integer)
 * @method int getShippingTotalAmount()
 * @method Builder setShippingTaxAmount($integer)
 * @method int getShippingTaxAmount()
 * @method Builder setShippingTitle($string)
 * @method string getShippingTitle()
 * @method Builder setShippingReference($integer)
 * @method int getShippingReference()
 * @method Builder setDiscountUnitPrice($integer)
 * @method int getDiscountUnitPrice()
 * @method Builder setDiscountTaxRate($integer)
 * @method int getDiscountTaxRate()
 * @method Builder setDiscountTotalAmount($integer)
 * @method int getDiscountTotalAmount()
 * @method Builder setDiscountTaxAmount($integer)
 * @method int getDiscountTaxAmount()
 * @method Builder setDiscountTitle($integer)
 * @method int getDiscountTitle()
 * @method Builder setDiscountReference($integer)
 * @method int getDiscountReference()
 * @method Builder setTaxUnitPrice($integer)
 * @method int getTaxUnitPrice()
 * @method Builder setTaxTotalAmount($integer)
 * @method int getTaxTotalAmount()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Builder extends DataObject implements BuilderInterface
{

    /**
     * @var string
     */
    public $prefix = '';
    /**
     * @var Collector
     */
    protected $orderLineCollector = null;
    /**
     * @var EventManager
     */
    protected $eventManager;
    /**
     * @var array
     */
    protected $orderLines = [];
    /**
     * @var \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote
     */
    protected $object = null;
    /**
     * @var array
     */
    protected $request = [];
    /**
     * @var bool
     */
    protected $inRequestSet = false;
    /**
     * @var ConfigHelper
     */
    protected $configHelper;
    /**
     * @var Url
     */
    protected $url;
    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;
    /**
     * @var KlarnaConfig
     */
    protected $klarnaConfig;
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var DataObject\Copy
     */
    private $objCopyService;
    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    private $addressRegistry;

    /**
     * Init
     *
     * @param EventManager                            $eventManager
     * @param Collector                               $collector
     * @param Url                                     $url
     * @param ConfigHelper                            $configHelper
     * @param DirectoryHelper                         $directoryHelper
     * @param DateTime\DateTime                       $coreDate
     * @param DataObject\Copy                         $objCopyService
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param KlarnaConfig                            $klarnaConfig
     * @param DataObjectFactory                       $dataObjectFactory
     * @param array                                   $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EventManager $eventManager,
        Collector $collector,
        Url $url,
        ConfigHelper $configHelper,
        DirectoryHelper $directoryHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\DataObject\Copy $objCopyService,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        KlarnaConfig $klarnaConfig,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->eventManager = $eventManager;
        $this->orderLineCollector = $collector;
        $this->url = $url;
        $this->configHelper = $configHelper;
        $this->directoryHelper = $directoryHelper;
        $this->coreDate = $coreDate;
        $this->objCopyService = $objCopyService;
        $this->addressRegistry = $addressRegistry;
        $this->klarnaConfig = $klarnaConfig;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Generate order body
     *
     * @param string $type
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateRequest($type = self::GENERATE_TYPE_CREATE)
    {
        $this->collectOrderLines($this->getObject()->getStore());
        return $this;
    }

    /**
     * Collect order lines
     *
     * @param StoreInterface $store
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collectOrderLines(StoreInterface $store)
    {
        /** @var \Klarna\Core\Model\Checkout\Orderline\AbstractLine $model */
        foreach ($this->getOrderLinesCollector()->getCollectors($store) as $model) {
            $model->collect($this);
        }

        return $this;
    }

    /**
     * Get totals collector model
     *
     * @return \Klarna\Core\Model\Checkout\Orderline\Collector
     */
    public function getOrderLinesCollector()
    {
        return $this->orderLineCollector;
    }

    /**
     * Get the object used to generate request
     *
     * @return \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set the object used to generate request
     *
     * @param \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote $object
     *
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get request
     *
     * @return array
     */
    abstract public function getRequest();

    /**
     * Set generated request
     *
     * @param array  $request
     * @param string $type
     *
     * @return $this
     */
    public function setRequest(array $request, $type = self::GENERATE_TYPE_CREATE)
    {
        $this->request = $this->cleanNulls($request);

        if (!$this->inRequestSet) {
            $this->inRequestSet = true;
            $this->eventManager->dispatch(
                $this->prefix . "_builder_set_request_{$type}",
                [
                    'builder' => $this
                ]
            );

            $this->eventManager->dispatch(
                $this->prefix . '_builder_set_request',
                [
                    'builder' => $this
                ]
            );
            $this->inRequestSet = false;
        }

        return $this;
    }

    /**
     * Remove items that are not allowed to be null
     *
     * @param array $request
     * @return array
     */
    protected function cleanNulls(array $request)
    {
        $disallowNulls = [
            'customer',
            'billing_address',
            'shipping_address',
            'external_payment_methods'
        ];
        foreach ($disallowNulls as $key) {
            if (empty($request[$key])) {
                unset($request[$key]);
            }
        }
        return $request;
    }

    /**
     * Get order lines as array
     *
     * @param StoreInterface $store
     * @param bool           $orderItemsOnly
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderLines(StoreInterface $store, $orderItemsOnly = false)
    {
        /** @var \Klarna\Core\Model\Checkout\Orderline\AbstractLine $model */
        foreach ($this->getOrderLinesCollector()->getCollectors($store) as $model) {
            if ($model->isIsTotalCollector() && $orderItemsOnly) {
                continue;
            }

            $model->fetch($this);
        }

        return $this->orderLines;
    }

    /**
     * Add an order line
     *
     * @param array $orderLine
     *
     * @return $this
     */
    public function addOrderLine(array $orderLine)
    {
        $this->orderLines[] = $orderLine;

        return $this;
    }

    /**
     * Remove all order lines
     *
     * @return $this
     */
    public function resetOrderLines()
    {
        $this->orderLines = [];

        return $this;
    }

    /**
     * Get merchant references
     *
     * @param $quote
     * @return DataObject
     */
    public function getMerchantReferences($quote)
    {
        $merchantReferences = $this->dataObjectFactory->create([
            'data' => [
                'merchant_reference_1' => $quote->getReservedOrderId(),
                'merchant_reference_2' => ''
            ]
        ]);

        $this->eventManager->dispatch(
            $this->prefix . '_merchant_reference_update',
            [
                'quote'                     => $quote,
                'merchant_reference_object' => $merchantReferences
            ]
        );
        return $merchantReferences;
    }

    /**
     * Get Terms URL
     *
     * @param $store
     * @param $configPath
     * @return mixed|string
     */
    public function getTermsUrl($store, $configPath = 'terms_url')
    {
        $termsUrl = $this->configHelper->getCheckoutConfig($configPath, $store);
        if (!empty($termsUrl) && !parse_url($termsUrl, PHP_URL_SCHEME)) {
            $termsUrl = $this->url->getDirectUrl($termsUrl, ['_nosid' => true]);
            return $termsUrl;
        }
        return $termsUrl;
    }

    /**
     * Populate prefill values
     *
     * @param $create
     * @param $quote
     * @param $store
     * @return mixed
     */
    public function prefill($create, $quote, $store)
    {
        /**
         * Customer
         */
        $create['customer'] = $this->getCustomerData($quote);

        /**
         * Billing Address
         */
        $create['billing_address'] = $this->getAddressData($quote, Address::TYPE_BILLING);

        /**
         * Shipping Address
         */
        if (isset($create['billing_address'])
            && $this->configHelper->isCheckoutConfigFlag('separate_address', $store)
        ) {
            $create['shipping_address'] = $this->getAddressData($quote, Address::TYPE_SHIPPING);
        }
        return $create;
    }

    /**
     * Get customer details
     *
     * @param $quote
     * @return array|null
     */
    public function getCustomerData($quote)
    {
        if (!$quote->getCustomerIsGuest() && $quote->getCustomerDob()) {
            return [
                'date_of_birth' => $this->coreDate->date('Y-m-d', $quote->getCustomerDob())
            ];
        }

        return null;
    }

    /**
     * Auto fill user address details
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param string                                $type
     *
     * @return array
     */
    protected function getAddressData($quote, $type = null)
    {
        $result = [];
        if ($quote->getCustomerEmail()) {
            $result['email'] = $quote->getCustomerEmail();
        }
        $customer = $quote->getCustomer();

        if ($quote->isVirtual() || $type === Address::TYPE_BILLING) {
            $address = $quote->getBillingAddress();

            if ($customer->getId() && !$address->getPostcode()) {
                $address = $this->getCustomerAddress($customer->getDefaultBilling());
            }
        } else {
            $address = $quote->getShippingAddress();

            if ($customer->getId() && !$address->getPostcode()) {
                $address = $this->getCustomerAddress($customer->getDefaultShipping());
            }
        }

        return $this->processAddress($result, $address);
    }

    /**
     * Retrieve customer address
     *
     * @param AddressInterface|string $address_id
     * @return CustomerAddress|AddressInterface
     */
    private function getCustomerAddress($address_id)
    {
        if (!$address_id) {
            return null;
        }
        if ($address_id instanceof AddressInterface) {
            return $address_id;
        }
        try {
            return $this->addressRegistry->retrieve($address_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $result
     * @param $address
     * @return array
     */
    private function processAddress($result, $address = null)
    {
        $resultObject = $this->dataObjectFactory->create(['data' => $result]);
        if ($address) {
            $address->explodeStreetAddress();
            $this->objCopyService->copyFieldsetToTarget(
                'sales_convert_quote_address',
                'to_klarna',
                $address,
                $resultObject
            );
            if ($address->getCountryId() === 'US') {
                $resultObject->setRegion($address->getRegionCode());
            }
        }

        $street_address = $this->prepareStreetAddressArray($resultObject);
        $resultObject->setStreetAddress($street_address[0]);
        $resultObject->setData('street_address2', $street_address[1]);

        if (isset($result['email'])) {
            $resultObject->setEmail($result['email']);
        }

        return array_filter($resultObject->toArray());
    }

    /**
     * @param $resultObject
     * @return array
     */
    private function prepareStreetAddressArray($resultObject)
    {
        $street_address = $resultObject->getStreetAddress();
        if (!is_array($street_address)) {
            $street_address = [$street_address];
        }
        if (count($street_address) === 1) {
            $street_address[] = '';
        }
        return $street_address;
    }

    /**
     * @param $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->setData('items', $items);
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->getData('items');
    }
}
