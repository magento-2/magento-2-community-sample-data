<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * Interface OptionsInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface OptionsInterface extends ApiObjectInterface
{
    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorText($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorCheckbox($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorCheckboxCheckmark($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorButton($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorBorder($colorCode);

    /**
     * Radius size, e.g. "5px"
     *
     * @param string $radiusBorder
     */
    public function setRadiusBorder($radiusBorder);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorHeader($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorBorderSelected($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorButtonText($colorCode);

    /**
     * CSS hex color, e.g. "#C0FFEE"
     *
     * @param string $colorCode
     */
    public function setColorLink($colorCode);
}
