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

use Klarna\Kp\Api\Data\CustomerInterface;

/**
 * Class Customer
 *
 * @package Klarna\Kp\Model\Api\Request
 */
class Customer implements CustomerInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $dob;

    /**
     * @var string
     */
    private $gender;

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
                $this->exports[] = $key;
            }
        }
    }

    /**
     * ISO 8601 date: The customer date of birth.
     *
     * @param string $dob
     */
    public function setDateOfBirth($dob)
    {
        $this->dob = $dob;
    }

    /**
     * Gender (male or female)
     *
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }
}
