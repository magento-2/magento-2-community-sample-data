<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Logger;

/**
 * Class Cleanser
 *
 * @package Klarna\Core\Logger
 */
class Cleanser
{
    /**
     * List of keys that should never be logged
     *
     * @var string[]
     */
    public $privateKeys = [
        'password',
        'shared_secret',
        'secret',
        'secret:Klarna\XMLRPC\Klarna:private',
        '_secret:Klarna_Checkout_BasicConnector:private',
        'date_of_birth',
        '_secret',
        'street',
        'Authorization',
        'given_name',
        'firstname',
        'gender',
        'family_name',
        'lastname',
        'email',
        'street_address',
        'phone',
        'telephone',
        'title'
    ];

    /**
     * Value to be substituted instead of sensitve data
     *
     * @var string
     */
    public $replaceValue = '** REMOVED **';

    /**
     * Search object for sensitive data and replace it if found
     *
     * @param mixed $input
     * @return mixed
     */
    public function checkForSensitiveData($input = null)
    {
        if (is_string($input)) {
            return $input;
        }
        if (null === $input) {
            return $input;
        }
        if ($input instanceof \Exception) {
            $newinput = [
                'message' => $input->getMessage(),
                'trace'   => $input->getTrace()
            ];
            $input = $newinput;
        }
        if (is_object($input)) {
            $input = $this->processObject($input);
        }
        if (is_array($input)) {
            return $this->checkArrayForSensitiveData($input);
        }
        if (($json = json_decode($input, true)) && is_array($json)) {
            $json = $this->checkForSensitiveData($json);
            return json_encode($json);
        }
        return $input;
    }

    /**
     * Convert object to array
     *
     * @param \stdClass $input
     * @return array
     */
    private function processObject($input)
    {
        if (method_exists($input, 'toArray')) {
            return $input->toArray();
        }
        if (method_exists($input, 'getData')) {
            return $input->getData();
        }
        return get_object_vars($input);
    }

    /**
     * Recursively search array for sensitive data and replace it if found
     *
     * @param array $input
     * @return array
     */
    public function checkArrayForSensitiveData($input)
    {
        foreach ($input as $key => $value) {
            if (is_int($key)) {
                continue;
            }
            if (in_array($key, $this->privateKeys)) {
                $input[$key] = $this->replaceValue;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            foreach ($value as $k => $v) {
                $input[$key][$k] = $this->checkForSensitiveData($v);
            }
        }
        return $input;
    }
}
