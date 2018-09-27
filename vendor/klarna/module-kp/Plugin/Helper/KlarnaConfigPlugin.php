<?php
/**
 * This file is part of the Klarna Kp module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Plugin\Helper;

use Klarna\Core\Api\VersionInterface;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Kp\Model\Payment\Kp;

/**
 * Class KlarnaConfigPlugin
 *
 * @package Klarna\Kp\Plugin\Helper
 */
class KlarnaConfigPlugin
{

    /**
     * @param KlarnaConfig     $subject
     * @param string           $result
     * @param VersionInterface $versionConfig
     * @param string           $methodCode
     * @return string
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterGetOmBuilderType(
        KlarnaConfig $subject,
        $result,
        VersionInterface $versionConfig,
        $methodCode = 'klarna_kp'
    ) {
        if ($methodCode !== 'klarna_kco') {
            return \Klarna\Kp\Model\Api\Builder\Kasper::class;
        }
        return $result;
    }
}
