<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Backend\Block\Widget\ContainerInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\Order;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\CountryGuard;

/**
 * Adds a debug button "Send Vertex Invoice" when configuration setting enabled
 *
 * @see \Magento\Sales\Block\Adminhtml\Order\View
 */
class SalesOrderViewBlockPlugin
{
    /** @var Config */
    private $config;

    /** @var Registry */
    private $registry;

    /** @var CountryGuard */
    private $countryGuard;

    /** @var UrlInterface */
    private $urlBuilder;

    /**
     * @param Config $config
     * @param Registry $registry
     * @param CountryGuard $countryGuard
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        Registry $registry,
        CountryGuard $countryGuard,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->countryGuard = $countryGuard;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Add button during setLayout
     *
     * {@see View::setLayout()} is the public method that calls the protected method {@see View::_prepareLayout()},
     * which is the method that pushes the button list up to the toolbar.  I had previously attempted to use
     * {@see View::toHtml()} only to later learn that step was far too late in the process.
     *
     * @see View::setLayout()
     *
     * @param View $subject
     * @param LayoutInterface $layout
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) interceptor required arguments
     */
    public function beforeSetLayout(View $subject, $layout)
    {
        $shouldAdd = $this->shouldAddInvoiceButton();

        if ($shouldAdd && $this->canInvoice()) {
            $this->addInvoiceButton($subject);
        } elseif ($shouldAdd) {
            $this->addCantInvoiceButton($subject);
        }
    }

    /**
     * Determine if we should add the manual invoice button
     *
     * @return bool
     */
    private function shouldAddInvoiceButton()
    {
        $order = $this->getOrder();
        if (!($order instanceof Order)) {
            return false;
        }

        $isActive = $this->config->isVertexActive($order->getStoreId());
        $shouldShow = $this->config->shouldShowManualButton();
        return $isActive && $shouldShow;
    }

    /**
     * Determine if we can invoice the order
     *
     * @return bool
     */
    private function canInvoice()
    {
        $order = $this->getOrder();
        return $order !== null && $this->countryGuard->isOrderServiceableByVertex($order);
    }

    /**
     * Add the manual invoice button to the sales_order_edit block, if it exists
     *
     * @param ContainerInterface $container
     * @return void
     */
    private function addInvoiceButton(ContainerInterface $container)
    {
        $confirmationMessage = __(
            'This will submit an invoice for the full amount to Vertex Cloud.\n' .
            'Do you want to continue?'
        )->render();

        $onclick = "confirmSetLocation('{$confirmationMessage}', '{$this->getInvoiceUrl()}')";

        $container->addButton(
            'vertex_invoice',
            [
                'label' => __('Vertex Invoice'),
                'onclick' => $onclick,
                'class' => 'vertex-invoice',
            ]
        );
    }

    /**
     * Add a manual invoice button to the sales_order_edit block that says why Vertex cannot invoice the order
     *
     * @param ContainerInterface $container
     */
    private function addCantInvoiceButton(ContainerInterface $container)
    {
        $errorMessage = __('Vertex Cloud can only process orders for the US and Canada at this time')->render();

        $onclick = "alert('{$errorMessage}')";

        $container->addButton(
            'vertex_cant_invoice',
            [
                'label' => __('Vertex Invoice'),
                'onclick' => $onclick,
                'class' => 'vertex-invoice disabled'
            ]
        );
    }

    /**
     * Get the URL for creating a manual invoice
     *
     * @return string
     */
    private function getInvoiceUrl()
    {
        return $this->urlBuilder->getUrl(
            'vertex/manualInvoice/send',
            ['order_id' => $this->getOrder()->getEntityId()]
        );
    }

    /**
     * Get the current Order
     *
     * @return Order|null
     */
    private function getOrder()
    {
        return $this->registry->registry('sales_order');
    }
}
