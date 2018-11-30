<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Email\Model\ResourceModel\Template as TemplateResource;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Filesystem\Driver\File as Filesystem;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Data setup for use during installation / upgrade
 *
 * @package Temando\Shipping\Setup
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class SetupData
{
    const ATTRIBUTE_CODE_LENGTH = 'ts_dimensions_length';
    const ATTRIBUTE_CODE_WIDTH = 'ts_dimensions_width';
    const ATTRIBUTE_CODE_HEIGHT = 'ts_dimensions_height';
    const PICKUP_ORDER_TEMPLATE = 'order_pickup_new.html';
    const PICKUP_ORDER_GUEST_TEMPLATE = 'order_pickup_new_guest.html';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Template factory
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var TemplateResource
     */
    private $templateResource;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var Filesystem
     */
    private $fileSystemDriver;

    /**
     * SetupData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param TemplateFactory $templateFactory
     * @param TemplateResource $templateResource
     * @param Reader $moduleReader
     * @param Filesystem $fileSystemDriver
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        TemplateFactory $templateFactory,
        TemplateResource $templateResource,
        Reader $moduleReader,
        Filesystem $fileSystemDriver
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->templateFactory = $templateFactory;
        $this->templateResource = $templateResource;
        $this->moduleReader = $moduleReader;
        $this->fileSystemDriver = $fileSystemDriver;
    }

    /**
     * Add dimension attributes. Need to be editable on store level due to the
     * weight unit (that dimensions unit is derived from) is configurable on
     * store level.
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function addDimensionAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_LENGTH, [
            'type' => 'decimal',
            'label' => 'Length',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 65,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_WIDTH, [
            'type' => 'decimal',
            'label' => 'Width',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 66,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);

        $eavSetup->addAttribute(Product::ENTITY, self::ATTRIBUTE_CODE_HEIGHT, [
            'type' => 'decimal',
            'label' => 'Height',
            'input' => 'text',
            'required' => false,
            'class' => 'not-negative-amount',
            'sort_order' => 67,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'user_defined' => false,
            'apply_to' => Type::TYPE_SIMPLE
        ]);
    }

    /**
     * Add new Pickup Order Email Template to DB.
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function addPickupOrderEmailTemplate()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('New Pickup Order');
        $template->setTemplateText($this->getEmailTemplate());
        $template->setTemplateType(TemplateTypesInterface::TYPE_HTML);
        $template->setTemplateSubject(
            '{{trans "Your %store_name order confirmation" store_name=$store.getFrontendName()}}'
        );
        $template->setOrigTemplateCode('sales_email_order_template');
        // @codingStandardsIgnoreLine
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var order.getEmailCustomerNote()":"Email Order Note","var order.increment_id":"Order Id","layout handle=\"sales_email_order_items\" order=$order area=\"frontend\"":"Order Items Grid","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var shipping_msg":"Shipping message"}');

        $this->templateResource->save($template);
    }

    /**
     * Add New Order Pickup Email Template.
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function addPickupOrderGuestEmailTemplate()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('New Pickup Order For Guest');
        $template->setTemplateText($this->getEmailTemplateForGuest());
        $template->setTemplateType(TemplateTypesInterface::TYPE_HTML);
        $template->setTemplateSubject(
            '{{trans "Your %store_name order confirmation" store_name=$store.getFrontendName()}}'
        );
        $template->setOrigTemplateCode('sales_email_order_guest_template');
        // @codingStandardsIgnoreLine
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var order.getEmailCustomerNote()":"Email Order Note","var order.getBillingAddress().getName()":"Guest Customer Name","var order.getCreatedAtFormatted(2)":"Order Created At (datetime)","var order.increment_id":"Order Id","layout handle=\"sales_email_order_items\" order=$order":"Order Items Grid","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var shipping_msg":"Shipping message"}');

        $this->templateResource->save($template);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getEmailTemplate()
    {
        $viewDir = $this->getDirectory();
        $templateContent = $this->fileSystemDriver->fileGetContents($viewDir . self::PICKUP_ORDER_TEMPLATE);

        return $templateContent;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getEmailTemplateForGuest()
    {
        $viewDir = $this->getDirectory();
        $templateContent = $this->fileSystemDriver->fileGetContents($viewDir . self::PICKUP_ORDER_GUEST_TEMPLATE);

        return $templateContent;
    }

    /**
     * @return string
     */
    private function getDirectory()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Temando_Shipping'
        );

        return $viewDir . '/frontend/email/';
    }
}
