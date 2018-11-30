<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_TwoFactorAuth
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\TwoFactorAuth\Model;

use Magento\Framework\Event\ManagerInterface;

class Alert implements AlertInterface
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * Trigger a security suite event
     * @param string $module
     * @param string $message
     * @param string $level
     * @param string $username
     * @param string $action
     * @param array|string $payload
     * @return boolean
     */
    public function event($module, $message, $level = null, $username = null, $action = null, $payload = null)
    {
        if ($level === null) {
            $level = self::LEVEL_INFO;
        }

        $params = [
            AlertInterface::ALERT_PARAM_LEVEL => $level,
            AlertInterface::ALERT_PARAM_MODULE => $module,
            AlertInterface::ALERT_PARAM_MESSAGE => $message,
            AlertInterface::ALERT_PARAM_USERNAME => $username,
            AlertInterface::ALERT_PARAM_PAYLOAD => $payload,
        ];

        $genericEvent = AlertInterface::EVENT_PREFIX . '_event';
        $moduleEvent = AlertInterface::EVENT_PREFIX . '_event_' . strtolower($module);
        $severityEvent = AlertInterface::EVENT_PREFIX . '_level_' . strtolower($level);

        $this->eventManager->dispatch($genericEvent, $params);
        $this->eventManager->dispatch($moduleEvent, $params);
        $this->eventManager->dispatch($severityEvent, $params);

        if ($action) {
            $actionEvent = AlertInterface::EVENT_PREFIX . '_action_' . strtolower($action);
            $this->eventManager->dispatch($actionEvent, $params);
        }

        return true;
    }
}
