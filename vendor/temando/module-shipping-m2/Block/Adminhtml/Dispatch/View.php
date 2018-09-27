<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Dispatch;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Container;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\DispatchProviderInterface;

/**
 * Temando Dispatch Layout Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 */
class View extends Container
{
    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * @var TimezoneInterfaceFactory
     */
    private $timezoneFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param DispatchProviderInterface $dispatchProvider
     * @param TimezoneInterfaceFactory $timezoneFactory
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        DispatchProviderInterface $dispatchProvider,
        TimezoneInterfaceFactory $timezoneFactory,
        array $data = []
    ) {
        $this->dispatchProvider = $dispatchProvider;
        $this->timezoneFactory  = $timezoneFactory;

        parent::__construct($context, $data);
    }

    /**
     * Add Back Button.
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->getDispatchesPageUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->addButton('back', $buttonData);

        return parent::_prepareLayout();
    }

    /**
     * Obtain dispatches grid url
     *
     * @return string
     */
    public function getDispatchesPageUrl()
    {
        return $this->getUrl('temando/dispatch/index');
    }

    /**
     * Obtain url for troubleshooting failed dispatches
     *
     * @return string
     */
    public function getSolveUrl()
    {
        return $this->getUrl('temando/dispatch/solve', [
            'dispatch_id' => $this->getDispatch()->getDispatchId()
        ]);
    }

    /**
     * @return DispatchInterface|null
     */
    public function getDispatch()
    {
        return $this->dispatchProvider->getDispatch();
    }

    /**
     * Obtain date. Parent method fails to convert date format returned from api.
     *
     * @see formatDate()
     * @see Timezone::formatDateTime()
     *
     * @param string $date
     * @return \DateTime
     */
    public function getDate($date)
    {
        $timezone = $this->timezoneFactory->create();
        $localizedDate = $timezone->date(new \DateTime($date));

        return $localizedDate;
    }
}
