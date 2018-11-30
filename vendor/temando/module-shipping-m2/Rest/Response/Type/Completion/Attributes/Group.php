<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Completion\Attributes;

/**
 * Temando API Completion Group Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Group
{
    /**
     * @var string
     */
    private $ref;

    /**
     * @var string
     */
    private $manifestReference;

    /**
     * @var string
     */
    private $pickupReference;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group\Charge[]
     */
    private $charges = [];

    /**
     * @var string
     */
    private $integrationId;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @var string
     */
    private $carrierMessage;

    /**
     * @var string
     */
    private $originId;

    /**
     * @var \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    private $documentation = [];

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     * @return void
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    /**
     * @return string
     */
    public function getManifestReference()
    {
        return $this->manifestReference;
    }

    /**
     * @param string $manifestReference
     * @return void
     */
    public function setManifestReference($manifestReference)
    {
        $this->manifestReference = $manifestReference;
    }

    /**
     * @return string
     */
    public function getPickupReference()
    {
        return $this->pickupReference;
    }

    /**
     * @param string $pickupReference
     * @return void
     */
    public function setPickupReference($pickupReference)
    {
        $this->pickupReference = $pickupReference;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group\Charge[]
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Completion\Attributes\Group\Charge[] $charges
     * @return void
     */
    public function setCharges(array $charges)
    {
        $this->charges = $charges;
    }

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->integrationId;
    }

    /**
     * @param string $integrationId
     * @return void
     */
    public function setIntegrationId($integrationId)
    {
        $this->integrationId = $integrationId;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
        return $this->carrierName;
    }

    /**
     * @param string $carrierName
     * @return void
     */
    public function setCarrierName($carrierName)
    {
        $this->carrierName = $carrierName;
    }

    /**
     * @return string
     */
    public function getCarrierMessage()
    {
        return $this->carrierMessage;
    }

    /**
     * @param string $carrierMessage
     * @return void
     */
    public function setCarrierMessage($carrierMessage)
    {
        $this->carrierMessage = $carrierMessage;
    }

    /**
     * @return string
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * @param string $originId
     * @return void
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Type\Generic\Documentation[]
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Type\Generic\Documentation[] $documentation
     * @return void
     */
    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }
}
