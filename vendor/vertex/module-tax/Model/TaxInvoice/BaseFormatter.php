<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxInvoice;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ModuleManager;
use Vertex\Tax\Model\Repository\TaxClassNameRepository;

/**
 * Provide most Formatted data for an Invoice Request
 */
class BaseFormatter
{
    const TRANSACTION_TYPE = 'SALE';

    /** @var Config */
    private $config;

    /** @var CountryInformationAcquirerInterface */
    private $countryInfoAcquirer;

    /** @var ModuleManager */
    private $moduleManager;

    /** @var TaxClassNameRepository */
    private $taxClassNameRepository;

    /**
     * @param Config $config
     * @param CountryInformationAcquirerInterface $countryInfoAcquirer
     * @param ModuleManager $moduleManager
     * @param TaxClassNameRepository $taxClassNameRepository
     */
    public function __construct(
        Config $config,
        CountryInformationAcquirerInterface $countryInfoAcquirer,
        ModuleManager $moduleManager,
        TaxClassNameRepository $taxClassNameRepository
    ) {
        $this->config = $config;
        $this->countryInfoAcquirer = $countryInfoAcquirer;
        $this->moduleManager = $moduleManager;
        $this->taxClassNameRepository = $taxClassNameRepository;
    }

    /**
     * Add properly formatted seller data to an Invoice Request line item
     *
     * @param array $data
     * @param string|null $store
     * @return array
     */
    public function addFormattedSellerData($data, $store = null)
    {
        $regionId = $this->config->getCompanyRegionId($store);

        $country = null;
        try {
            $country = $this->countryInfoAcquirer->getCountryInfo($this->config->getCompanyCountry($store));
            $countryName = $country->getThreeLetterAbbreviation();
        } catch (NoSuchEntityException $exception) {
            $countryName = '';
        }

        $companyState = null;
        if ($country !== null) {
            foreach ($country->getAvailableRegions() as $region) {
                if ($region->getId() == $regionId) {
                    $companyState = $region->getCode();
                    break;
                }
            }
        }

        $data['location_code'] = $this->config->getLocationCode($store);
        $data['transaction_type'] = static::TRANSACTION_TYPE;
        $data['company_id'] = $this->config->getCompanyCode($store);
        $data['company_street_1'] = $this->config->getCompanyStreet1($store);
        $data['company_street_2'] = $this->config->getCompanyStreet2($store);
        $data['company_city'] = $this->config->getCompanyCity($store);
        $data['company_state'] = $companyState;
        $data['company_postcode'] = $this->config->getCompanyPostalCode($store);
        $data['company_country'] = $countryName;
        $data['trusted_id'] = $this->config->getTrustedId($store);

        return $data;
    }

    /**
     * Add properly formatted address data to an Invoice Request line item
     *
     * @param array $data
     * @param Address $address
     * @return array
     */
    public function addFormattedAddressData($data, $address)
    {
        $data['customer_street1'] = $address->getStreetLine(1);
        $data['customer_street2'] = $address->getStreetLine(2);
        $data['customer_city'] = $address->getCity();
        $data['customer_region'] = $address->getRegionCode();
        $data['customer_postcode'] = $address->getPostcode();

        try {
            $country = $this->countryInfoAcquirer->getCountryInfo(
                $this->config->getCompanyCountry($address->getOrder()->getStoreId())
            );
            $countryName = $country->getThreeLetterAbbreviation();
        } catch (NoSuchEntityException $exception) {
            $countryName = '';
        }

        $data['customer_country'] = $countryName;
        $data['tax_area_id'] = $address->getData('tax_area_id');

        return $data;
    }

    /**
     * Add Refund Adjustments to all line items if this is a Creditmemo
     *
     * @param array $info
     * @param Creditmemo $creditmemoModel
     * @return array
     */
    public function addRefundAdjustments($info, $creditmemoModel)
    {
        if (!($creditmemoModel instanceof Creditmemo)) {
            return $info;
        }

        if ($creditmemoModel->getBaseAdjustmentPositive()) {
            $itemData = [];

            $itemData['product_class'] = $this->reduceCode(
                $this->taxClassNameRepository->getById(
                    $this->config->getCreditmemoAdjustmentPositiveClass($creditmemoModel->getStoreId())
                )
            );
            $itemData['product_code'] = $this->reduceCode(
                $this->config->getCreditmemoAdjustmentPositiveCode(
                    $creditmemoModel->getStoreId()
                )
            );
            $itemData['qty'] = 1;
            $itemData['price'] = -1 * $creditmemoModel->getBaseAdjustmentPositive();
            $itemData['extended_price'] = $itemData['price'];

            $info[] = $itemData;
        }

        if ($creditmemoModel->getBaseAdjustmentNegative()) {
            $itemData = [];

            $itemData['product_class'] = $this->reduceCode(
                $this->taxClassNameRepository->getById(
                    $this->config->getCreditmemoAdjustmentFeeClass($creditmemoModel->getStoreId())
                )
            );
            $itemData['product_code'] = $this->reduceCode(
                $this->config->getCreditmemoAdjustmentFeeCode($creditmemoModel->getStoreId())
            );
            $itemData['qty'] = 1;
            $itemData['price'] = $creditmemoModel->getBaseAdjustmentNegative();
            $itemData['extended_price'] = $itemData['price'];

            $info[] = $itemData;
        }

        return $info;
    }

    /**
     * Get properly formatted Shipping Data for a Tax Invoice request
     *
     * @param Order|Invoice|Creditmemo $originalEntity
     * @param string|null $event
     * @return array
     */
    public function getFormattedShippingData($originalEntity = null, $event = null)
    {
        $itemData = [];

        $order = $originalEntity instanceof Order ? $originalEntity : $originalEntity->getOrder();

        if ($order && $order->getShippingMethod() && $this->isFirstOfPartial($originalEntity)) {
            $itemData['product_class'] = $this->reduceCode(
                $this->taxClassNameRepository->getById(
                    $this->config->getShippingTaxClassId($originalEntity->getStoreId())
                )
            );
            $itemData['product_code'] = $this->reduceCode($order->getShippingMethod());
            if ($originalEntity instanceof Creditmemo) {
                $itemData['price'] = $originalEntity->getBaseShippingAmount()
                    - $originalEntity->getBaseShippingDiscountAmount();
            } else {
                $itemData['price'] = $order->getBaseShippingAmount() - $order->getBaseShippingDiscountAmount();
            }
            $itemData['qty'] = 1;
            $itemData['extended_price'] = $itemData['price'];

            if ($event === 'cancel' || $event === 'refund') {
                $itemData['price'] = -1 * $itemData['price'];
                $itemData['extended_price'] = -1 * $itemData['extended_price'];
            }
        }

        return $itemData;
    }

    /**
     * Get properly formatted order-level giftwrapping line-item data for an Invoice Request
     *
     * @param Order $order
     * @param Order|Invoice|Creditmemo $originalEntity
     * @param string|null $event
     * @return array
     */
    public function getFormattedOrderGiftWrap($order, $originalEntity = null, $event = null)
    {
        if (!$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return null;
        }

        $itemData = [];

        if ($originalEntity !== null && !$this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }

        $itemData['product_class'] = $this->reduceCode(
            $this->taxClassNameRepository->getById(
                $this->config->getGiftWrappingOrderClass($order->getStoreId())
            )
        );
        $itemData['product_code'] = $this->reduceCode(
            $this->config->getGiftWrappingOrderCode($order->getStoreId())
        );
        $itemData['qty'] = 1;
        $itemData['price'] = $order->getGwBasePrice();
        if (empty($itemData['price'])) {
            $itemData['price'] = 0;
        }
        $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];

        if ($event === 'cancel' || $event === 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * Get properly formatted Order-level Printed Card line item data for an Invoice Request
     *
     * @param Order $order
     * @param Order|Invoice|Creditmemo $originalEntity
     * @param string|null $event
     * @return array
     */
    public function getFormattedOrderPrintCard($order, $originalEntity = null, $event = null)
    {
        if (!$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            return null;
        }

        $itemData = [];

        if ($originalEntity !== null && !$this->isFirstOfPartial($originalEntity)) {
            return $itemData;
        }

        $itemData['product_class'] = $this->reduceCode(
            $this->taxClassNameRepository->getById(
                $this->config->getPrintedGiftcardClass($order->getStoreId())
            )
        );
        $itemData['product_code'] = $this->reduceCode(
            $this->config->getPrintedGiftcardCode($order->getStoreId())
        );
        $itemData['qty'] = 1;
        $itemData['price'] = $order->getGwCardBasePrice();
        if (empty($itemData['price'])) {
            $itemData['price'] = 0;
        }
        $itemData['extended_price'] = $itemData['qty'] * $itemData['price'];

        if ($event === 'cancel' || $event === 'refund') {
            $itemData['price'] = -1 * $itemData['price'];
            $itemData['extended_price'] = -1 * $itemData['extended_price'];
        }

        return $itemData;
    }

    /**
     * Determine if this is the first of a series of partial invoices
     *
     * @param Order|Invoice|Creditmemo $originalEntity
     * @return bool
     */
    private function isFirstOfPartial($originalEntity)
    {
        if ($originalEntity instanceof Invoice) {
            if (!$originalEntity->getBaseShippingTaxAmount()) {
                return false;
            }
        }

        if ($originalEntity instanceof Order && $originalEntity->getBaseShippingInvoiced() &&
            $this->config->requestByInvoiceCreation($originalEntity->getStore())) {
            return false;
        }

        if ($originalEntity instanceof Creditmemo) {
            if (!$originalEntity->getBaseShippingAmount()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reduce tax code to the maximum allowed characters
     *
     * @param string $code
     * @return string
     */
    private function reduceCode($code)
    {
        return substr($code, 0, Config::MAX_CHAR_PRODUCT_CODE_ALLOWED);
    }
}
