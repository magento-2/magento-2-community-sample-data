<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Controller\Adminhtml\ManualInvoice;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;
use Vertex\Tax\Model\TaxInvoice;

/**
 * "Send Vertex Invoice" button action
 *
 * Used only for debugging purposes
 */
class Send extends Action
{
    /** @var OrderInterface */
    private $order;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Config */
    private $config;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var TaxInvoice */
    private $taxInvoice;

    /**
     * @param Action\Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param TaxInvoice $taxInvoice
     */
    public function __construct(
        Action\Context $context,
        OrderRepositoryInterface $orderRepository,
        Config $config,
        CountryGuard $countryGuard,
        TaxInvoice $taxInvoice
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->taxInvoice = $taxInvoice;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function execute()
    {
        $order = $this->getOrder();
        $orderModel = $this->getOrderModel($order);
        if (!$order || !$orderModel || !$this->config->isVertexActive($order->getStoreId())) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        if ($this->canInvoice($order)) {
            $invoiceRequestData = $this->taxInvoice->prepareInvoiceData($orderModel);
            $invoiceSuccess = $this->taxInvoice->sendInvoiceRequest($invoiceRequestData, $orderModel);
            if (is_array($invoiceRequestData) && $invoiceSuccess) {
                // success message added by taxInvoice
                return $this->createOrderRedirect($order);
            }
            // error message added by taxInvoice
            return $this->createOrderRedirect($order);
        }

        $this->messageManager->addSuccessMessage(__('Order is not applicable for Vertex Invoicing')->render());
        return $this->createOrderRedirect($order);
    }

    /**
     * Create a redirect object pointing to the order view
     *
     * @param OrderInterface $order
     * @return Redirect
     */
    private function createOrderRedirect(OrderInterface $order)
    {
        return $this->resultRedirectFactory->create()
            ->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
    }

    /**
     * Determine if we can invoice an order
     *
     * @param OrderInterface $order
     * @return bool
     */
    private function canInvoice(OrderInterface $order)
    {
        return $this->countryGuard->isOrderServiceableByVertex($this->getOrderModel($order));
    }

    /**
     * Retrieve the Order
     *
     * Uses the request parameter order_id if we have not already stored an Order in state
     *
     * @return bool|OrderInterface
     */
    private function getOrder()
    {
        if ($this->order !== null) {
            return $this->order;
        }
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists')->render());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists')->render());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->order = $order;
        return $order;
    }

    /**
     * Convert OrderInterface to Order
     *
     * Actual conversion not currently implemented
     *
     * @param OrderInterface $order
     * @return Order|null
     */
    private function getOrderModel(OrderInterface $order)
    {
        if ($order instanceof Order) {
            return $order;
        }
        return null;
    }
}
