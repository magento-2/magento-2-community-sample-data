<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\System\Config\Source;

use Klarna\Core\Model\System\Config\Source\Base;

/**
 * Class Version
 *
 * @package Klarna\Kp\Model\Config\Source
 */
class Version extends Base
{
    /**
     * @var string
     */
    protected $optionName = 'payments_api_versions';
}
