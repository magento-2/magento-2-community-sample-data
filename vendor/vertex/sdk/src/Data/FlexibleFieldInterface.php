<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Data;

/**
 * Represents a generic Flexible Field
 *
 * This is expected to be subclassed by flexible field types that the mappers will understand and convert appropriately
 *
 * @api
 */
interface FlexibleFieldInterface
{
    /**
     * Retrieve the field identifier
     *
     * @return int|null
     */
    public function getFieldId();

    /**
     * Retrieve the field value
     *
     * @return mixed|null
     */
    public function getFieldValue();

    /**
     * Set the field identifier
     *
     * @param int $fieldId
     * @return FlexibleFieldInterface
     */
    public function setFieldId($fieldId);

    /**
     * Set the field value
     *
     * @param mixed $fieldValue
     * @return FlexibleFieldInterface
     */
    public function setFieldValue($fieldValue);
}
