<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Mapper;

use Vertex\Exception\ValidationException;

/**
 * Contains functional utilities for mapping classes
 */
class MapperUtilities
{
    /**
     * Default maximum value for any string sent to Vertex
     */
    const DEFAULT_MAX = 60;

    /**
     * Default minimum value for any string sent to Vertex
     */
    const DEFAULT_MIN = 1;

    /**
     * Validate and, if valid, add a date to the mapping based on it's key
     *
     * @param \stdClass $mapping
     * @param \DateTimeInterface $value
     * @param string $key
     * @param bool $optional
     * @param string $name Human readable name
     * @return \stdClass
     * @throws ValidationException
     */
    public function addToMapWithDateValidation(\stdClass $mapping, $value, $key, $optional = true, $name = null)
    {
        $name = $name ?: $key;

        if ($this->assertNotNull($value, $optional, $name)
            && $this->assertDate($value, $name)
        ) {
            $mapping->{$key} = $value->format('Y-m-d');
        }

        return $mapping;
    }

    /**
     * Validate and, if valid, add a float to the mapping based on its key
     *
     * @param \stdClass $mapping
     * @param float $value
     * @param string $key
     * @param int $min
     * @param int $max
     * @param bool $optional
     * @param string $name Human readable name
     * @return \stdClass
     * @throws ValidationException
     */
    public function addToMapWithDecimalValidation(
        \stdClass $mapping,
        $value,
        $key,
        $min = PHP_INT_MIN,
        $max = PHP_INT_MAX,
        $optional = true,
        $name = null
    ) {
        $name = $name ?: $key;

        if ($this->assertNotNull($value, $optional, $name)
            && $this->assertDecimal($value, $name)
            && $this->assertBetween($value, $min, $max, $name)
        ) {
            $mapping->{$key} = (float)$value;
        }

        return $mapping;
    }

    /**
     * Validate and, if valid, add an enumeration to the mapping based on its key
     *
     * @param \stdClass $mapping
     * @param string $value
     * @param string $key
     * @param string[] $availableValues
     * @param bool $optional
     * @param string $name Human readable name
     * @return \stdClass
     * @throws ValidationException
     */
    public function addToMapWithEnumerationValidation(
        \stdClass $mapping,
        $value,
        $key,
        array $availableValues,
        $optional = true,
        $name = null
    ) {
        $name = $name ?: $key;

        if ($this->assertNotNull($value, $optional, $name)
            && $this->assertContains($value, $availableValues, $name)
        ) {
            $mapping->{$key} = $value;
        }

        return $mapping;
    }

    /**
     * Validate and, if valid, add an integer to the mapping based on its key
     *
     * @param \stdClass $mapping
     * @param int $value
     * @param string $key
     * @param int $min
     * @param int $max
     * @param bool $optional
     * @param string $name Human readable name
     * @return \stdClass
     * @throws ValidationException
     */
    public function addToMapWithIntegerValidation(
        \stdClass $mapping,
        $value,
        $key,
        $min = PHP_INT_MIN,
        $max = PHP_INT_MAX,
        $optional = true,
        $name = null
    ) {
        $name = $name ?: $key;

        if ($this->assertNotNull($value, $optional, $name)
            && $this->assertInteger($value, $name)
            && $this->assertBetween($value, $min, $max, $name)
        ) {
            $mapping->{$key} = (int)$value;
        }

        return $mapping;
    }

    /**
     * Validate and, if valid, add a string to the mapping based on it's key
     *
     * @param \stdClass $mapping
     * @param string $value
     * @param string $key
     * @param int $min
     * @param int $max
     * @param bool $optional
     * @param string $name Human readable name
     * @return \stdClass
     * @throws ValidationException
     */
    public function addToMapWithLengthValidation(
        \stdClass $mapping,
        $value,
        $key,
        $min = self::DEFAULT_MIN,
        $max = self::DEFAULT_MAX,
        $optional = true,
        $name = null
    ) {
        $name = $name ?: $key;

        if ($this->assertNotNull($value, $optional, $name)
            && $this->assertLength($value, $min, $max, $name)
        ) {
            $mapping->{$key} = (string)$value;
        }

        return $mapping;
    }

    /**
     * Assert that a value is between a minimum and maximum integer
     *
     * @param int|float $value
     * @param int $min
     * @param int $max
     * @param string $exceptionName Name to call the variable in the exception
     * @return true
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws ValidationException
     */
    private function assertBetween($value, $min = PHP_INT_MIN, $max = PHP_INT_MAX, $exceptionName = 'Value')
    {
        $failure = $value < $min || $value > $max;
        $message = "{$exceptionName} must be a valid integer";
        if ($failure && $min !== PHP_INT_MIN && $max !== PHP_INT_MAX) {
            $message = "{$exceptionName} must be between {$min} and {$max}, inclusive";
        }
        if ($failure && $min === PHP_INT_MIN && $max !== PHP_INT_MAX) {
            $message = "{$exceptionName} must be less than or equal to {$max}";
        }
        if ($failure && $max === PHP_INT_MAX && $min !== PHP_INT_MIN) {
            $message = "{$exceptionName} must be greater than or equal to {$min}";
        }
        if ($failure) {
            throw new ValidationException($message);
        }
        return true;
    }

    /**
     * Assert that a value is contained in an array
     *
     * @param mixed $value
     * @param array $array
     * @param string $exceptionName Name to call the variable in the exception
     * @param bool $strict Check types as well
     * @return true
     * @throws ValidationException
     */
    private function assertContains($value, array $array, $exceptionName = 'Value', $strict = true)
    {
        if (!in_array($value, $array, $strict)) {
            throw new ValidationException("{$exceptionName} must be one of " . implode(', ', $array));
        }

        return true;
    }

    /**
     * Assert that a value is a \DateTimeInterface
     *
     * @param mixed $value
     * @param string $exceptionName Name to call the variable in the exception
     * @return true
     * @throws ValidationException
     */
    private function assertDate($value, $exceptionName = 'Value')
    {
        $isObject = is_object($value);
        $isDateTime = $value instanceof \DateTime;
        $implementsInterface = $isObject && in_array('DateTimeInterface', class_implements($value), true);
        if (!$isDateTime && !$implementsInterface) {
            throw new ValidationException("{$exceptionName} must be a valid DateTimeInterface");
        }

        return true;
    }

    /**
     * Assert that a value is a float or an integer
     *
     * @param mixed $value
     * @param string $exceptionName Name to call the variable in the exception
     * @return true
     * @throws ValidationException
     */
    private function assertDecimal($value, $exceptionName = 'Value')
    {
        if (!is_numeric($value)) {
            throw new ValidationException("{$exceptionName} must be a float");
        }

        return true;
    }

    /**
     * Assert that a value is an integer
     *
     * @param mixed $value
     * @param string $exceptionName Name to call the variable in the exception
     * @return true
     * @throws ValidationException
     */
    private function assertInteger($value, $exceptionName = 'Value')
    {
        if (!is_numeric($value) || (int)$value != (float)$value) {
            throw new ValidationException("{$exceptionName} must be an integer");
        }

        return true;
    }

    /**
     * Assert that a value's string length is between a minimum and maximum amount of characters
     *
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @param string $exceptionName Name to call the variable in the exception
     * @return true
     * @throws ValidationException
     */
    private function assertLength($value, $min = self::DEFAULT_MIN, $max = self::DEFAULT_MAX, $exceptionName = 'Value')
    {
        $length = mb_strlen($value);
        if ($length < $min || $length > $max) {
            throw new ValidationException(
                "{$exceptionName} length ({$length}) must be between {$min} and {$max} characters, inclusive"
            );
        }

        return true;
    }

    /**
     * Assert that a value is not null
     *
     * @param mixed $value
     * @param bool $optional When set to false, an exception will be thrown
     * @param string $exceptionName Name to call the variable in the exception
     * @return bool Whether or not the value was null
     * @throws ValidationException
     */
    private function assertNotNull($value, $optional = true, $exceptionName = 'Value')
    {
        if (!$optional && $value === null) {
            throw new ValidationException("{$exceptionName} must not be null");
        }

        return $value !== null;
    }
}
