<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Order;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Metadata\CustomerMetadata;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * View model for customer related information.
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CustomerDetails implements ArgumentInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var CustomerMetadataInterface|CustomerMetadata
     */
    private $customerMetadata;

    /**
     * @var ElementFactory
     */
    private $metadataElementFactory;

    /**
     * OrderDetails constructor.
     * @param Escaper $escaper
     * @param UrlInterface $urlBuilder
     * @param GroupRepositoryInterface $customerGroupRepository
     * @param CustomerMetadataInterface $customerMetadata
     * @param ElementFactory $metadataElementFactory
     */
    public function __construct(
        Escaper $escaper,
        UrlInterface $urlBuilder,
        GroupRepositoryInterface $customerGroupRepository,
        CustomerMetadataInterface $customerMetadata,
        ElementFactory $metadataElementFactory
    ) {
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerMetadata = $customerMetadata;
        $this->metadataElementFactory = $metadataElementFactory;
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    private function prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->prepareAccountDataSortOrder($data, $sortOrder + 1);
        }

        return $sortOrder;
    }

    /**
     * Get URL to edit the customer.
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getCustomerViewUrl
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getCustomerViewUrl(OrderInterface $order)
    {
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return '';
        }

        return $this->urlBuilder->getUrl('customer/index/edit', ['id' => $order->getCustomerId()]);
    }

    /**
     * Return name of the customer group.
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getCustomerGroupName
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getCustomerGroupName(OrderInterface $order)
    {
        $customerGroupId = $order->getCustomerGroupId();
        if ($customerGroupId === null) {
            return '';
        }

        try {
            $customerGroup = $this->customerGroupRepository->getById($customerGroupId);
            return $customerGroup->getCode();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @see \Magento\Sales\Block\Adminhtml\Order\View\Info::getCustomerAccountData
     *
     * @param OrderInterface|\Magento\Sales\Model\Order $order
     * @return mixed[]
     */
    public function getCustomerAccountData(OrderInterface $order)
    {
        $accountData = [];
        $entityType = 'customer';

        foreach ($this->customerMetadata->getAllAttributesMetadata($entityType) as $attribute) {
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $orderKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $order->getData($orderKey);
            if ($orderValue != '') {
                $metadataElement = $this->metadataElementFactory->create($attribute, $orderValue, $entityType);
                $value = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder = $attribute->getSortOrder() + $attribute->isUserDefined() ? 200 : 0;
                $sortOrder = $this->prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escaper->escapeHtml($value, ['br']),
                ];
            }
        }
        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }
}
