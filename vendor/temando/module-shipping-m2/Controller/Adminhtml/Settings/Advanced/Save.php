<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Settings\Advanced;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\EventStream\StreamRepositoryInterface;

/**
 * Temando Save Advanced Settings Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Save extends Action
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var StreamRepositoryInterface
     */
    private $streamRepository;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param ModuleConfigInterface $config
     * @param StreamRepositoryInterface $streamRepository
     */
    public function __construct(
        Context $context,
        ModuleConfigInterface $config,
        StreamRepositoryInterface $streamRepository
    ) {
        $this->config = $config;
        $this->streamRepository = $streamRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        $sync = (bool) $request->getParam('sync_enable', false);
        $syncShipment = (bool) $request->getParam('sync_shipment', false);
        $syncOrder = (bool) $request->getParam('sync_order', false);

        try {
            if ($sync && !$this->config->isSyncEnabled()) {
                // sync was switched on, save everything
                $this->streamRepository->save($this->config->getStreamId());
                $this->config->saveSyncEnabled($sync);
                $this->config->saveSyncShipmentEnabled($syncShipment);
                $this->config->saveSyncOrderEnabled($syncOrder);
                $this->messageManager->addSuccessMessage(__('Settings saved successfully, stream created.'));
            } elseif ($sync && $this->config->isSyncEnabled()) {
                // sync is still on, save sync types only
                $this->config->saveSyncShipmentEnabled($syncShipment);
                $this->config->saveSyncOrderEnabled($syncOrder);
                $this->messageManager->addSuccessMessage(__('Settings saved successfully.'));
            } elseif (!$sync && $this->config->isSyncEnabled()) {
                // sync was switched off, save state only
                $this->streamRepository->delete($this->config->getStreamId());
                $this->config->saveSyncEnabled($sync);
                $this->messageManager->addSuccessMessage(__('Settings saved successfully, stream removed.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e);
            $resultRedirect->setPath('temando/settings_advanced/edit');
            return $resultRedirect;
        }

        $resultRedirect->setPath('adminhtml/system_config/edit', [
            'section' => 'carriers',
            '_fragment' => 'carriers_temando-link',
        ]);

        return $resultRedirect;
    }
}
