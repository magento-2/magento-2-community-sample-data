<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Order;

/**
 * Temando API Order Deliver To Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DeliverTo
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Address
     */
    private $address;

    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Contact
     */
    private $contact;

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Address $address
     * @return void
     */
    public function setAddress(\Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Contact $contact
     * @return void
     */
    public function setContact(\Temando\Shipping\Rest\Response\Fields\Order\DeliverTo\Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string[] $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
