<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Cron;

use Temando\Shipping\Model\Config\ModuleConfig;
use Temando\Shipping\Sync\EventStreamProcessor;

/**
 * Process events as dispatched by the Temando platform.
 *
 * The Temando Shipping module is one out of many applications that handle the
 * merchant's entities at the platform. If another application (ERP, WMS, etc.)
 * edits entities at the platform, the M2 instance will be notified and the
 * local counterpart will be updated accordingly.
 *
 * @package  Temando\Shipping\Cron
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ProcessPlatformEvents
{
    /**
     * @var EventStreamProcessor
     */
    private $processor;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @param EventStreamProcessor $processor
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(EventStreamProcessor $processor, ModuleConfig $moduleConfig)
    {
        $this->processor    = $processor;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Run cron if it is enabled via module config.
     *
     * @return void
     */
    public function execute()
    {
        if ($this->moduleConfig->isSyncEnabled()) {
            $this->processor->processEvents(100);
        }
    }
}
