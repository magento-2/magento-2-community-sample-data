<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Block\Info;

use Klarna\Core\Model\MerchantPortal;
use Klarna\Core\Model\OrderRepository;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Klarna
 *
 * @package Klarna\Core\Block\Info
 * @api
 */
class Klarna extends \Magento\Payment\Block\Info
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * Klarna Order Repository
     *
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Resolver
     */
    private $locale;

    /**
     * @var MerchantPortal
     */
    private $merchantPortal;

    /**
     * Klarna constructor.
     *
     * @param Context           $context
     * @param OrderRepository   $orderRepository
     * @param MerchantPortal    $merchantPortal
     * @param Resolver          $locale
     * @param DataObjectFactory $dataObjectFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        MerchantPortal $merchantPortal,
        Resolver $locale,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderRepository = $orderRepository;
        $this->_template = 'Klarna_Core::payment/info.phtml';
        $this->locale = $locale;
        $this->merchantPortal = $merchantPortal;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Return locale info
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecificInformation()
    {
        $data = parent::getSpecificInformation();
        $transport = $this->dataObjectFactory->create(['data' => $data]);
        $info = $this->getInfo();
        $order = $info->getOrder();
        try {
            $klarnaOrder = $this->orderRepository->getByOrder($order);

            if ($klarnaOrder->getId() && $klarnaOrder->getKlarnaOrderId()) {
                $transport->setData((string)__('Order ID'), $klarnaOrder->getKlarnaOrderId());

                $this->addReservationToDisplay($transport, $klarnaOrder);
                $this->addMerchantPortalLinkToDisplay($transport, $order, $klarnaOrder);
            }
        } catch (NoSuchEntityException $e) {
            $transport->setData((string)__('Error'), $e->getMessage());
        }

        $klarnaReferenceId = $info->getAdditionalInformation('klarna_reference');
        if ($klarnaReferenceId) {
            $transport->setData((string)__('Reference'), $klarnaReferenceId);
        }

        $this->addInvoicesToDisplay($transport, $order);

        return $transport->getData();
    }

    /**
     * Add Klarna Reservation ID to order view
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @param \Klarna\Core\Api\OrderInterface     $klarnaOrder
     */
    private function addReservationToDisplay($transport, $klarnaOrder)
    {
        if ($klarnaOrder->getReservationId()
            && $klarnaOrder->getReservationId() != $klarnaOrder->getKlarnaOrderId()
        ) {
            $transport->setData((string)__('Reservation'), $klarnaOrder->getReservationId());
        }
    }

    /**
     * Add Klarna Merchant Portal link to order view
     *
     * @param \Magento\Framework\DataObject|array    $transport
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Klarna\Core\Api\OrderInterface        $klarnaOrder
     */
    private function addMerchantPortalLinkToDisplay($transport, $order, $klarnaOrder)
    {
        //get merchant portal link
        $merchantPortalLink = $this->merchantPortal->getOrderMerchantPortalLink($order, $klarnaOrder);
        if ($merchantPortalLink) {
            $transport->setData(
                (string)__('Merchant Portal'),
                $this->merchantPortal->getOrderMerchantPortalLink($order, $klarnaOrder)
            );
        }
    }

    /**
     * Add invoices to order view
     *
     * @param \Magento\Framework\DataObject|array    $transport
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    private function addInvoicesToDisplay($transport, $order)
    {
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            if ($invoice->getTransactionId()) {
                $invoiceKey = (string)__('Invoice ID (#%1)', $invoice->getIncrementId());
                $transport->setData($invoiceKey, $invoice->getTransactionId());
            }
        }
    }

    /**
     * Check if string is a url
     *
     * @param string $string
     * @return bool
     */
    public function isStringUrl($string)
    {
        return (bool)filter_var($string, FILTER_VALIDATE_URL);
    }
}
