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

/**
 * @api
 */
interface AlertInterface
{
    const EVENT_PREFIX = 'twofactor';

    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warn';
    const LEVEL_ERROR = 'error';
    const LEVEL_SECURITY_ALERT = 'security_alert';

    const ACTION_LOG = 'log';
    const ACTION_LOCKDOWN = 'lockdown';

    const ALERT_PARAM_LEVEL = 'level';
    const ALERT_PARAM_MODULE = 'module';
    const ALERT_PARAM_MESSAGE = 'message';
    const ALERT_PARAM_USERNAME = 'username';
    const ALERT_PARAM_PAYLOAD = 'payload';

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
    public function event($module, $message, $level = null, $username = null, $action = null, $payload = null);
}
