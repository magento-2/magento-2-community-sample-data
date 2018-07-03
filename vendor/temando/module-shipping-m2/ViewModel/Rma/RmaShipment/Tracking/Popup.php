<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\ViewModel\Rma\RmaShipment\Tracking;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\ResourceModel\Repository\ShipmentRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Rma\RmaAccess;
use Temando\Shipping\Model\Shipment;
use Temando\Shipping\Model\ShipmentInterface;
use Temando\Shipping\Model\Shipping\Carrier;
use Temando\Shipping\Model\Shipment\TrackEventInterface;

/**
 * View model for tracking popup.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Popup implements ArgumentInterface
{
    /**
     * @var RmaAccess
     */
    private $rmaAccess;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Carrier
     */
    private $carrier;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * Copyright information
     *
     * @var string
     */
    private $copyright;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Popup constructor.
     * @param RmaAccess $rmaAccess
     * @param UrlInterface $urlBuilder
     * @param Carrier $carrier
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param TimezoneInterface $timezone
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RmaAccess $rmaAccess,
        UrlInterface $urlBuilder,
        Carrier $carrier,
        DateTimeFormatterInterface $dateTimeFormatter,
        TimezoneInterface $timezone,
        ShipmentRepositoryInterface $shipmentRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->rmaAccess = $rmaAccess;
        $this->urlBuilder = $urlBuilder;
        $this->carrier = $carrier;
        $this->shipmentRepository = $shipmentRepository;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->localeDate = $timezone;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get return shipment from platform.
     *
     * @return ShipmentInterface
     */
    private function getShipment()
    {
        return $this->rmaAccess->getCurrentRmaShipment();
    }

    /**
     * Get carrier name from fulfillment.
     *
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrier->getConfigData('title');
    }

    /**
     * Get tracking number from fulfillment.
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        /** @var Shipment $shipment */
        $shipment = $this->getShipment();
        $trackingId = $shipment->getFulfillment()->getTrackingReference();

        return $trackingId;
    }

    /**
     * Get tracking progress info
     *
     * @return string[]
     */
    public function getTrackingProgressInfo()
    {
        try {
            $trackEvents = $this->shipmentRepository->getTrackingById($this->getShipment()->getShipmentId());
            $trackEventsData = array_map(function (TrackEventInterface $trackEvent) {
                return $trackEvent->getEventData();
            }, $trackEvents);
        } catch (LocalizedException $e) {
            $trackEventsData = [];
        }

        return $trackEventsData;
    }

    /**
     * @return string
     */
    public function getCarrierTitle()
    {
        $shipment = $this->getShipment();
        $carrierTitle = sprintf(
            '%s - %s',
            $shipment->getFulfillment()->getCarrierName(),
            $shipment->getFulfillment()->getServiceName()
        );

        return $carrierTitle;
    }

    /**
     * @param string[] $trackEventsData
     * @return string
     */
    public function getTrackingStatus(array $trackEventsData)
    {
        if (empty($trackEventsData)) {
            return $this->getShipment()->getStatus();
        }

        return $trackEventsData[0]['activity'];
    }

    /**
     * Format given date and time in current locale without changing timezone
     *
     * @param string $date
     * @param string $time
     * @return string
     */
    public function formatDeliveryDateTime($date, $time)
    {
        return $this->formatDeliveryDate($date) . ' ' . $this->formatDeliveryTime($time);
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDeliveryDate($date)
    {
        $format = $this->localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        return $this->dateTimeFormatter->formatObject($this->localeDate->date(new \DateTime($date)), $format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param string $time
     * @param string $date
     * @return string
     */
    public function formatDeliveryTime($time, $date = null)
    {
        if (!empty($date)) {
            $time = $date . ' ' . $time;
        }

        $format = $this->localeDate->getTimeFormat(\IntlDateFormatter::SHORT);
        return $this->dateTimeFormatter->formatObject($this->localeDate->date(new \DateTime($time)), $format);
    }

    /**
     * Retrieve copyright information
     *
     * @return string
     */
    public function getCopyright()
    {
        if (!$this->copyright) {
            $this->copyright = $this->scopeConfig->getValue(
                'design/footer/copyright',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return __($this->copyright);
    }
}
