<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Logger\Handlers;

class File extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/klarna_api.log';
}
