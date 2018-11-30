<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Sales\Order\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Block\Adminhtml\Order\View\Info as SalesOrderInfo;
use Temando\Shipping\Model\ResourceModel\Order\OrderRepository;
use Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Temando Shipment Info Layout Block
 *
 * @deprecated since 1.2.0 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Shipment\Location
 *
 * @package  Temando\Shipping\Block
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class Info extends SalesOrderInfo
{
    /**
     * @var \Temando\Shipping\Model\ShipmentInterface
     */
    private $extShipment;

    /**
     * @var ShipmentProviderInterface
     */
    private $shipmentProvider;

    /**
     * @var OrderAddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param ShipmentProviderInterface $shipmentProvider
     * @param OrderAddressInterfaceFactory $addressFactory,
     * @param OrderRepository $orderRepository
     * @param mixed[] $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        ShipmentProviderInterface $shipmentProvider,
        OrderAddressInterfaceFactory $addressFactory,
        OrderRepository $orderRepository,
        array $data = []
    ) {
        $this->shipmentProvider = $shipmentProvider;
        $this->addressFactory = $addressFactory;
        $this->orderRepository = $orderRepository;

        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $data
        );
    }

    /**
     * Declare External Shipment instance
     *
     * @return  \Temando\Shipping\Model\ShipmentInterface
     */
    public function getExtShipment()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        if ($this->extShipment === null) {
            if ($extShipment = $this->shipmentProvider->getShipment()) {
                $this->extShipment = $extShipment;
            } else {
                $this->extShipment = $this->_coreRegistry->registry('ext_shipment');
            }
        }

        return $this->extShipment;
    }

    /**
     * Check if the current order has a shipment at the Temando platform.
     * If so, it qualifies for some additional data to be displayed.
     *
     * @return bool
     */
    public function hasExtShipment()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->getExtShipment();
        return isset($shipment);
    }

    /**
     * @return string
     */
    public function getExtShipmentId()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        if ($shipment = $this->getExtShipment()) {
            return $shipment->getShipmentId();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getExtOrderId()
    {
        try {
            /** @var \Temando\Shipping\Api\Data\Order\OrderReferenceInterface $orderReference */
            $orderReference = $this->orderRepository->getReferenceByOrderId($this->getOrder()->getId());

            return $orderReference->getExtOrderId();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getFormattedDestinationAddress()
    {
        /** @var \Temando\Shipping\Model\ShipmentInterface $shipment */
        $shipment = $this->getExtShipment();
        if (!$shipment) {
            return '';
        }

        $destinationLocation = $shipment->getDestinationLocation();
        $addressData = [
            'region'     => $destinationLocation->getRegionCode(),
            'postcode'   => $destinationLocation->getPostalCode(),
            'lastname'   => $destinationLocation->getPersonLastName(),
            'street'     => $destinationLocation->getStreet(),
            'city'       => $destinationLocation->getCity(),
            'email'      => $destinationLocation->getEmail(),
            'telephone'  => $destinationLocation->getPhoneNumber(),
            'country_id' => $destinationLocation->getCountryCode(),
            'firstname'  => $destinationLocation->getPersonFirstName(),
            'company'    => $destinationLocation->getCompany()
        ];
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address $address */
        $address = $this->addressFactory->create(['data' => $addressData]);
        $formattedAddress = $this->addressRenderer->format($address, 'html');

        return (string) $formattedAddress;
    }
}
