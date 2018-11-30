<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Completion\Attributes;

/**
 * Temando API Completion Shipment Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
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
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $isPaperless;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment\Error[]
     */
    private $errors = [];

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
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
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function getIsPaperless()
    {
        return $this->isPaperless;
    }

    /**
     * @param bool $isPaperless
     * @return void
     */
    public function setIsPaperless($isPaperless)
    {
        $this->isPaperless = $isPaperless;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment\Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Shipment\Error[] $errors
     * @return void
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
