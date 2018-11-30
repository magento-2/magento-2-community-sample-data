<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Api;

/**
 * Trait Export
 *
 * @package Klarna\Kp\Model\Api
 */
trait Export
{

    /**
     * Exportable class fields
     *
     * @var array
     */
    public $exports = [];

    /**
     * Generate array object needed for API call
     *
     * @return array
     */
    public function toArray()
    {
        $data = [];
        if (!is_array($this->exports)) {
            return $data;
        }
        foreach ($this->exports as $export) {
            if ($this->$export !== null) {
                $data[$export] = $this->$export;
            }
        }
        return $data;
    }
}
