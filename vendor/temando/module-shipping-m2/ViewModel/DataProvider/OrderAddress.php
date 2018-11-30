<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Order address formatter
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderAddress
{
    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * OrderAddress constructor.
     * @param AddressRenderer $addressRenderer
     */
    public function __construct(AddressRenderer $addressRenderer)
    {
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @param OrderAddressInterface|Address $address
     * @return string
     */
    public function getFormattedAddress(OrderAddressInterface $address)
    {
        $formattedAddress = $this->addressRenderer->format($address, 'html');
        return (string)$formattedAddress;
    }
}
