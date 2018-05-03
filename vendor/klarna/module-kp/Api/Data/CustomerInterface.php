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
 * Interface CustomerInterface
 *
 * @package Klarna\Kp\Api\Data
 */
interface CustomerInterface extends ApiObjectInterface
{
    /**
     * ISO 8601 date: The customer date of birth.
     *
     * @param string $dob
     */
    public function setDateOfBirth($dob);

    /**
     * Gender (male or female)
     *
     * @param string $gender
     */
    public function setGender($gender);
}
