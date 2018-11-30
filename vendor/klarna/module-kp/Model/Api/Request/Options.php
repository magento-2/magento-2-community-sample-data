<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Kp\Api\Data\OptionsInterface;

/**
 * Class Options
 *
 * @package Klarna\Kp\Model\Api\Request
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Options implements OptionsInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $color_button;

    /**
     * @var string
     */
    private $color_button_text;

    /**
     * @var string
     */
    private $color_checkbox;

    /**
     * @var string
     */
    private $color_checkbox_checkmark;

    /**
     * @var string
     */
    private $color_header;

    /**
     * @var string
     */
    private $color_link;

    /**
     * @var string
     */
    private $color_border;

    /**
     * @var string
     */
    private $color_border_selected;

    /**
     * @var string
     */
    private $color_text;

    /**
     * @var string
     */
    private $radius_border;

    /**
     * @var string
     */
    private $allow_separate_shipping_address;

    /**
     * @var string
     */
    private $phone_mandatory;

    /**
     * @var string
     */
    private $date_of_birth_mandatory;

    /**
     * @var string
     */
    private $require_validate_callback_success;

    /**
     * @var string
     */
    private $title_mandatory;

    /**
     * @var string
     */
    private $payment_review;

    /**
     * @var string
     */
    private $color_text_secondary;

    /**
     * @var string
     */
    private $color_details;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->addExport($key);
            }
        }
    }

    /**
     * Make sure property has been added to export list
     *
     * @param string $key
     */
    private function addExport($key)
    {
        if (!in_array($key, $this->exports, true)) {
            $this->exports[] = $key;
        }
    }

    /**
     * Allow separate shipping address from billing address
     *
     * @param string $allow_separate_shipping_address
     */
    public function setAllowSeparateShippingAddress($allow_separate_shipping_address)
    {
        $this->allow_separate_shipping_address = $allow_separate_shipping_address;
        $this->addExport('allow_separate_shipping_address');
    }

    /**
     * Require phone number
     *
     * @param string $phone_mandatory
     */
    public function setPhoneMandatory($phone_mandatory)
    {
        $this->phone_mandatory = $phone_mandatory;
        $this->addExport('phone_mandatory');
    }

    /**
     * Require DOB
     *
     * @param string $date_of_birth_mandatory
     */
    public function setDateOfBirthMandatory($date_of_birth_mandatory)
    {
        $this->date_of_birth_mandatory = $date_of_birth_mandatory;
        $this->addExport('date_of_birth_mandatory');
    }

    /**
     * Require that the validate callback is successful
     *
     * @param string $require_validate_callback_success
     */
    public function setRequireValidateCallbackSuccess($require_validate_callback_success)
    {
        $this->require_validate_callback_success = $require_validate_callback_success;
        $this->addExport('require_validate_callback_success');
    }

    /**
     * Make title (Mr, Mrs, Ms, etc..) required
     *
     * @param string $title_mandatory
     */
    public function setTitleMandatory($title_mandatory)
    {
        $this->title_mandatory = $title_mandatory;
        $this->addExport('title_mandatory');
    }

    /**
     * @param string $payment_review
     */
    public function setPaymentReview($payment_review)
    {
        $this->payment_review = $payment_review;
        $this->addExport('payment_review');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $color_text_secondary
     */
    public function setColorTextSecondary($color_text_secondary)
    {
        $this->color_text_secondary = $color_text_secondary;
        $this->addExport('color_text_secondary');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $color_details
     */
    public function setColorDetails($color_details)
    {
        $this->color_details = $color_details;
        $this->addExport('color_details');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorText($colorCode)
    {
        $this->color_text = $colorCode;
        $this->addExport('color_text');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorCheckbox($colorCode)
    {
        $this->color_checkbox = $colorCode;
        $this->addExport('color_checkbox');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorCheckboxCheckmark($colorCode)
    {
        $this->color_checkbox_checkmark = $colorCode;
        $this->addExport('color_checkbox_checkmark');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorButton($colorCode)
    {
        $this->color_button = $colorCode;
        $this->addExport('color_button');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorBorder($colorCode)
    {
        $this->color_border = $colorCode;
        $this->addExport('color_border');
    }

    /**
     * Radius size, e.g. "5px"
     *
     * @param string $radiusBorder
     */
    public function setRadiusBorder($radiusBorder)
    {
        $this->radius_border = $radiusBorder;
        $this->addExport('radius_border');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorHeader($colorCode)
    {
        $this->color_header = $colorCode;
        $this->addExport('color_header');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorBorderSelected($colorCode)
    {
        $this->color_border_selected = $colorCode;
        $this->addExport('color_border_selected');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorButtonText($colorCode)
    {
        $this->color_button_text = $colorCode;
        $this->addExport('color_button_text');
    }

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorLink($colorCode)
    {
        $this->color_link = $colorCode;
        $this->addExport('color_link');
    }
}
