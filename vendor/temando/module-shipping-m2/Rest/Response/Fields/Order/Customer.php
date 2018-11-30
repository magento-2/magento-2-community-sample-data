<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\Order;

use Temando\Shipping\Rest\Response\Fields\Order\Customer\Contact;

/**
 * Temando API Order Customer Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Customer
{
    /**
     * @var \Temando\Shipping\Rest\Response\Fields\Order\Customer\Contact
     */
    private $contact;

    /**
     * @return \Temando\Shipping\Rest\Response\Fields\Order\Customer\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param \Temando\Shipping\Rest\Response\Fields\Order\Customer\Contact $contact
     * @return void
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }
}
