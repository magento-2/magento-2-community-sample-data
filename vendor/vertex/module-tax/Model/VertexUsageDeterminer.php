<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;

/**
 * Business logic for determining if Vertex should be used for tax calculation
 */
class VertexUsageDeterminer
{
    /** @var AddressDeterminer */
    private $addressDeterminer;

    /** @var Config */
    private $config;

    /** @var CountryGuard */
    private $countryGuard;

    /**
     * @param Config $config
     * @param CountryGuard $countryGuard
     * @param AddressDeterminer $addressDeterminer
     */
    public function __construct(Config $config, CountryGuard $countryGuard, AddressDeterminer $addressDeterminer)
    {
        $this->config = $config;
        $this->countryGuard = $countryGuard;
        $this->addressDeterminer = $addressDeterminer;
    }

    /**
     * Determine whether or not to use Vertex to calculate taxes for an address
     *
     * @param string|null $storeCode
     * @param AddressInterface|null $address
     * @param int|null $customerId
     * @param bool $isVirtual
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function shouldUseVertex(
        $storeCode = null,
        $address = null,
        $customerId = null,
        $isVirtual = false
    ) {
        if ($address !== null && !($address instanceof AddressInterface || $address instanceof QuoteAddressInterface)) {
            throw new \InvalidArgumentException(
                '$address must be a Customer or Quote Address.  Is: '
                . (is_object($address) ? get_class($address) : gettype($address))
            );
        }

        $address = $this->addressDeterminer->determineAddress($address, $customerId, $isVirtual);

        return $this->config->isVertexActive($storeCode)
            && !$this->config->isDisplayPriceInCatalogEnabled($storeCode)
            && $address !== null
            && $address->getCountryId()
            && $this->countryGuard->isCountryIdServiceableByVertex($address->getCountryId());
    }
}
