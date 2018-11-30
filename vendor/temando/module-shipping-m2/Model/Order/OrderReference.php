<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Order;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Data\Collection\AbstractDb;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Order\OrderReference as OrderReferenceResource;

/**
 * Reference to order entity created at Temando platform
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class OrderReference extends AbstractModel implements OrderReferenceInterface
{
    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * OrderReference constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->shippingExperienceFactory = $shippingExperienceFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(OrderReferenceResource::class);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(OrderReferenceInterface::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setData(OrderReferenceInterface::ORDER_ID, $orderId);
    }

    /**
     * @return string
     */
    public function getExtOrderId()
    {
        return $this->getData(OrderReferenceInterface::EXT_ORDER_ID);
    }

    /**
     * @param string $extOrderId
     * @return void
     */
    public function setExtOrderId($extOrderId)
    {
        $this->setData(OrderReferenceInterface::EXT_ORDER_ID, $extOrderId);
    }

    /**
     * @return \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[]
     */
    public function getShippingExperiences()
    {
        return $this->getData(OrderReferenceInterface::SHIPPING_EXPERIENCES);
    }

    /**
     * @param \Temando\Shipping\Api\Data\Order\ShippingExperienceInterface[] $shippingExperiences
     * @return void
     */
    public function setShippingExperiences(array $shippingExperiences)
    {
        $this->setData(OrderReferenceInterface::SHIPPING_EXPERIENCES, $shippingExperiences);
    }
}
