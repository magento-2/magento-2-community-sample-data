<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response\Type\Batch\Attributes;

/**
 * Temando API Batch Shipment Response Type
 *
 * @package  Temando\Batch\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Shipment
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\ErrorResponseType[]
     */
    private $errors;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\ErrorResponseType[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\ErrorResponseType[] $errors
     *
     * @return void
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
