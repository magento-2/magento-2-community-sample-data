<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Vertex\Tax\Api\ClientInterface;
use Vertex\Tax\Exception\ApiRequestException;
use Vertex\Tax\Exception\ApiRequestException\AuthenticationException;
use Vertex\Tax\Exception\ApiRequestException\ConnectionFailureException;
use Vertex\Tax\Model\Request\Customer;
use Vertex\Tax\Model\TaxInvoice\BaseFormatter;
use Vertex\Tax\Model\TaxInvoice\ItemFormatter;

/**
 * Service for sending Tax Invoices to the Vertex API
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxInvoice
{
    const REQUEST_TYPE = 'invoice';
    const REQUEST_TYPE_REFUND = 'invoice_refund';

    /** @var LoggerInterface */
    private $logger;

    /** @var ManagerInterface */
    private $messageManager;

    /** @var ClientInterface */
    private $vertex;

    /** @var Customer */
    private $customerFormatter;

    /** @var RequestItemFactory */
    private $requestItemFactory;

    /** @var DateTime */
    private $dateTime;

    /** @var OrderStatusHistoryRepositoryInterface */
    private $orderStatusRepository;

    /** @var BaseFormatter */
    private $baseFormatter;

    /** @var ItemFormatter */
    private $itemFormatter;

    /**
     * @param LoggerInterface $logger
     * @param ClientInterface $vertex
     * @param ManagerInterface $messageManager
     * @param Customer $customerFormatter
     * @param RequestItemFactory $requestItemFactory
     * @param DateTime $dateTime
     * @param OrderStatusHistoryRepositoryInterface $orderStatusRepository
     * @param BaseFormatter $baseFormatter
     * @param ItemFormatter $itemFormatter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LoggerInterface $logger,
        ClientInterface $vertex,
        ManagerInterface $messageManager,
        Customer $customerFormatter,
        RequestItemFactory $requestItemFactory,
        DateTime $dateTime,
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        BaseFormatter $baseFormatter,
        ItemFormatter $itemFormatter
    ) {
        $this->logger = $logger;
        $this->vertex = $vertex;
        $this->messageManager = $messageManager;
        $this->customerFormatter = $customerFormatter;
        $this->requestItemFactory = $requestItemFactory;
        $this->dateTime = $dateTime;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->baseFormatter = $baseFormatter;
        $this->itemFormatter = $itemFormatter;
    }

    /**
     * Prepare the data for the Invoice Request
     *
     * @param Order|Invoice|Creditmemo $entityItem
     * @param string|null $event
     * @return array
     * @throws LocalizedException
     */
    public function prepareInvoiceData($entityItem, $event = null)
    {
        try {
            /** @var RequestItem $requestItem */
            $requestItem = $this->requestItemFactory->create();
            $order = $entityItem;
            $typeId = 'ordered';

            if ($entityItem instanceof Invoice || $entityItem instanceof Creditmemo) {
                $order = $entityItem->getOrder();
                $typeId = 'invoiced';
            }

            $requestItem->setDocumentNumber($order->getIncrementId());
            $requestItem->setDocumentDate(
                $this->dateTime->date('Y-m-d', $this->dateTime->timestamp($order->getCreatedAt()))
            );
            $requestItem->setPostingDate($this->dateTime->date('Y-m-d'));

            $companyAddressInfo = $this->baseFormatter->addFormattedSellerData([], $order->getStoreId());

            $requestItem->setLocationCode($companyAddressInfo['location_code']);
            $requestItem->setTransactionType($companyAddressInfo['transaction_type']);
            $requestItem->setCompanyId($companyAddressInfo['company_id']);
            $requestItem->setCompanyStreet1($companyAddressInfo['company_street_1']);
            $requestItem->setCompanyStreet2($companyAddressInfo['company_street_2']);
            $requestItem->setCompanyCity($companyAddressInfo['company_city']);
            $requestItem->setCompanyState($companyAddressInfo['company_state']);
            $requestItem->setCompanyPostcode($companyAddressInfo['company_postcode']);
            $requestItem->setCompanyCountry($companyAddressInfo['company_country']);
            $requestItem->setTrustedId($companyAddressInfo['trusted_id']);

            $customerClass = $this->customerFormatter->taxClassNameByCustomerGroupId($order->getCustomerGroupId());
            $customerCode = $this->customerFormatter->getCustomerCodeById($order->getCustomerId());

            $requestItem->setCustomerClass($customerClass);
            $requestItem->setCustomerCode($customerCode);

            $address = $this->addressChooser($order);
            $customerAddressInfo = $this->baseFormatter->addFormattedAddressData([], $address);
            $requestItem->setCustomerStreet1($customerAddressInfo['customer_street1']);
            $requestItem->setCustomerStreet2($customerAddressInfo['customer_street2']);
            $requestItem->setCustomerCity($customerAddressInfo['customer_city']);
            $requestItem->setCustomerRegion($customerAddressInfo['customer_region']);
            $requestItem->setCustomerPostcode($customerAddressInfo['customer_postcode']);
            $requestItem->setCustomerCountry($customerAddressInfo['customer_country']);

            $orderItems = [];
            $orderedItems = $entityItem->getAllItems();
            foreach ($orderedItems as $item) {
                /** @var OrderItem|InvoiceItem|CreditmemoItem $item */
                $this->itemFormatter->addPreparedOrderItems($orderItems, $typeId, $event, $item);
            }

            $shippingInfo = $this->baseFormatter->getFormattedShippingData($entityItem, $event);
            if (!empty($shippingInfo) && !$order->getIsVirtual()) {
                $orderItems[] = $shippingInfo;
            }

            $orderItems = $this->baseFormatter->addRefundAdjustments($orderItems, $entityItem);
            $orderItems = $this->addIfNotEmpty(
                $orderItems,
                $this->baseFormatter->getFormattedOrderGiftWrap($order, $entityItem, $event),
                $this->baseFormatter->getFormattedOrderPrintCard($order, $entityItem, $event)
            );

            $requestItem->setRequestType('InvoiceRequest');
            $requestItem->setOrderItems($orderItems);
            return $requestItem->exportAsArray();
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Send the Invoice Request to the API
     *
     * @param array $data
     * @param Order $order
     * @return bool
     */
    public function sendInvoiceRequest($data, Order $order)
    {
        if ($this->vertex instanceof ApiClient) {
            try {
                $response = $this->vertex->performRequest(
                    $data,
                    static::REQUEST_TYPE,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                );
            } catch (ApiRequestException $e) {
                $this->addErrorMessage(__('Could not submit invoice to Vertex.'), $e);
                return false;
            }
        } else {
            $response = $this->vertex->sendApiRequest($data, static::REQUEST_TYPE, $order);
            if (!$response) {
                $this->addErrorMessage(__('Could not submit invoice to Vertex.'));
                return false;
            }
        }
        if (is_array($response['TotalTax'])) {
            $totalTax = $response['TotalTax']['_'];
        } else {
            $totalTax = $response['TotalTax'];
        }
        $comment = $order->addStatusHistoryComment(
            'Vertex Invoice sent successfully. Amount: $' . number_format($totalTax, 2)
        );
        try {
            $this->orderStatusRepository->save($comment);
        } catch (\Exception $originalException) {
            $exception = new \Exception('Could not save Vertex invoice comment', 0, $originalException);
            $this->logger->critical(
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
            );
        }
        return true;
    }

    /**
     * Send the Creditmemo Request to the API
     *
     * @param array $data
     * @param Order|null $order
     * @return bool
     */
    public function sendRefundRequest($data, Order $order)
    {
        if ($this->vertex instanceof ApiClient) {
            try {
                $response = $this->vertex->performRequest(
                    $data,
                    static::REQUEST_TYPE_REFUND,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                );
            } catch (ApiRequestException $e) {
                $this->addErrorMessage(__('Could not submit refund to Vertex.'), $e);
                return false;
            }
        } else {
            $response = $this->vertex->sendApiRequest($data, static::REQUEST_TYPE_REFUND, $order);
            if (!$response) {
                $this->addErrorMessage(__('Could not submit refund to Vertex.'));
                return false;
            }
        }
        if (is_array($response['TotalTax'])) {
            $totalTax = $response['TotalTax']['_'];
        } else {
            $totalTax = $response['TotalTax'];
        }
        $comment = $order->addStatusHistoryComment(
            'Vertex Invoice refunded successfully. Amount: $' . number_format($totalTax, 2)
        );
        try {
            $this->orderStatusRepository->save($comment);
        } catch (\Exception $originalException) {
            $exception = new CouldNotSaveException(
                __('Could not save Vertex invoice refund comment'),
                $originalException
            );
            $this->logger->critical(
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString()
            );
        }
        return true;
    }

    private function addErrorMessage(Phrase $friendlyPhrase, $exception = null)
    {
        $friendlyMessage = $friendlyPhrase->render();
        $errorMessage = null;

        $exceptionClass = get_class($exception);

        switch ($exceptionClass) {
            case AuthenticationException::class:
                $errorMessage = __(
                    '%1 Please verify your configured Company Code and Trusted ID are correct.',
                    $friendlyMessage
                );
                break;
            case ConnectionFailureException::class:
                $errorMessage = __(
                    '%1 Vertex could not be reached. Please verify your configuration.',
                    $friendlyMessage
                );
                break;
            case ApiRequestException::class:
            default:
                $errorMessage = __('%1 Error has been logged.', $friendlyMessage);
                break;
        }

        $this->messageManager->addErrorMessage($errorMessage);
    }

    /**
     * Determine which address taxes should be calculated for
     *
     * @param Order $order
     * @return mixed
     */
    private function addressChooser(Order $order)
    {
        return $order->getIsVirtual() ? $order->getBillingAddress() : $order->getShippingAddress();
    }

    /**
     * Append items to the provided array if they are not empty
     *
     * @param array $array
     * @param mixed ...$items
     * @return array
     */
    private function addIfNotEmpty(array $array)
    {
        $items = array_slice(func_get_args(), 1);

        foreach ($items as $item) {
            if (!empty($item)) {
                $array[] = $item;
            }
        }

        return $array;
    }
}
