<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Plugin\Quote\Address;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

/**
 * @package  Temando\Shipping\Plugin
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ToOrderAddressPlugin
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var OrderAddressExtensionInterfaceFactory
     */
    private $addressExtensionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ToOrderAddressPlugin constructor.
     *
     * @param ModuleConfigInterface                 $config
     * @param AddressRepositoryInterface            $addressRepository
     * @param OrderAddressExtensionInterfaceFactory $addressExtensionFactory
     * @param StoreManagerInterface                 $storeManager
     */
    public function __construct(
        ModuleConfigInterface $config,
        AddressRepositoryInterface $addressRepository,
        OrderAddressExtensionInterfaceFactory $addressExtensionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config                  = $config;
        $this->addressRepository       = $addressRepository;
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->storeManager            = $storeManager;
    }

    /**
     * Copy extension attributes (dynamic fields selected in checkout) to order
     * shipping address. When the order gets placed in Magento, these checkout
     * fields must be transmitted to the API. There is no need to persist them
     * to data storage though.
     *
     * @see \Temando\Shipping\Plugin\Sales\OrderRepositoryPlugin::afterSave
     *
     * @param ToOrderAddress $subject
     * @param OrderAddressInterface $orderAddress
     * @param Address $quoteAddress
     * @return OrderAddressInterface
     */
    public function afterConvert(
        ToOrderAddress $subject,
        OrderAddressInterface $orderAddress,
        Address $quoteAddress
    ) {
        if (!$this->config->isEnabled($this->storeManager->getStore()->getId())) {
            return $orderAddress;
        }

        if ($quoteAddress->getAddressType() !== Address::ADDRESS_TYPE_SHIPPING) {
            // no need to handle billing addresses
            return $orderAddress;
        }

        try {
            $checkoutAddress = $this->addressRepository->getByQuoteAddressId($quoteAddress->getId());
        } catch (NoSuchEntityException $e) {
            // no additional fields selected during checkout
            return $orderAddress;
        }

        $extensionAttributes = $orderAddress->getExtensionAttributes();
        if (!$extensionAttributes instanceof OrderAddressExtensionInterface) {
            $extensionAttributes = $this->addressExtensionFactory->create();
        }

        $extensionAttributes->setCheckoutFields($checkoutAddress->getServiceSelection());
        $orderAddress->setExtensionAttributes($extensionAttributes);

        return $orderAddress;
    }
}
