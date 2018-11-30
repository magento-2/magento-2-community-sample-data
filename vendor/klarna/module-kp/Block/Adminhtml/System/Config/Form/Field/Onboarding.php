<?php

/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Klarna\Core\Helper\VersionInfo;
use Klarna\Core\Model\System;

/**
 * Getting back the Klarna Merchant Onboarding text with link
 *
 * Class Onboarding
 * @package Klarna\Kp\Block\Adminhtml\System\Config\Form\Field
 */
class Onboarding extends Field
{
    /** @var Onboarding $onboarding */
    private $onboarding;

    /** @var VersionInfo $versionInfo */
    private $versionInfo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param System\Onboarding $onboarding
     * @param VersionInfo $versionInfo
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        System\Onboarding $onboarding,
        VersionInfo $versionInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->onboarding = $onboarding;
        $this->versionInfo = $versionInfo;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $moduleVersion = $this->versionInfo->getVersion('Klarna_Kp');
        $moduleKey = 'kp';

        $url = $this->onboarding->getUrl($moduleKey, $moduleVersion);
        if (empty($url)) {
            return parent::render($element);
        }

        $urlText = __('link');
        $urlTag = '<p style="display:inline"><a href="' . $url . '" target="_blank">' . $urlText . '</a></span>';

        $text = __('Click on this %1 to visit the Klarna Merchant Onboarding Page and request credentials.', $urlTag);
        return $text;
    }
}
