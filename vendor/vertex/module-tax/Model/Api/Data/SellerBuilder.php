<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Api\Data;

use Vertex\Data\SellerInterface;
use Vertex\Data\SellerInterfaceFactory;
use Vertex\Tax\Model\Config;

/**
 * Create a {@see SellerInterface} from store configuration
 */
class SellerBuilder
{
    /** @var AddressBuilder */
    private $addressBuilder;

    /** @var Config */
    private $config;

    /** @var SellerInterfaceFactory */
    private $sellerFactory;

    /** @var string */
    private $scopeCode;

    /** @var string */
    private $scopeType;

    /**
     * @param SellerInterfaceFactory $sellerFactory
     * @param Config $config
     * @param AddressBuilder $addressBuilder
     */
    public function __construct(SellerInterfaceFactory $sellerFactory, Config $config, AddressBuilder $addressBuilder)
    {
        $this->sellerFactory = $sellerFactory;
        $this->config = $config;
        $this->addressBuilder = $addressBuilder;
    }

    /**
     * Create a {@see SellerInterface} from store configuration
     *
     * @return SellerInterface
     */
    public function build()
    {
        /** @var SellerInterface $seller */
        $seller = $this->sellerFactory->create();

        $street = [
            $this->config->getCompanyStreet1($this->scopeCode, $this->scopeType),
            $this->config->getCompanyStreet2($this->scopeCode, $this->scopeType)
        ];

        $address = $this->addressBuilder
            ->setStreet($street)
            ->setCity($this->config->getCompanyCity($this->scopeCode, $this->scopeType))
            ->setRegionId($this->config->getCompanyRegionId($this->scopeCode, $this->scopeType))
            ->setPostalCode($this->config->getCompanyPostalCode($this->scopeCode, $this->scopeType))
            ->setCountryCode($this->config->getCompanyCountry($this->scopeCode, $this->scopeType))
            ->build();

        $seller->setPhysicalOrigin($address);

        if ($this->config->getCompanyCode($this->scopeCode, $this->scopeType)) {
            $seller->setCompanyCode($this->config->getCompanyCode($this->scopeCode, $this->scopeType));
        }

        return $seller;
    }

    /**
     * Set the Scope Code
     *
     * @param string|null $scopeCode
     * @return SellerBuilder
     */
    public function setScopeCode($scopeCode)
    {
        $this->scopeCode = $scopeCode;
        return $this;
    }

    /**
     * Set the Scope Type
     *
     * @param string|null $scopeType
     * @return SellerBuilder
     */
    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;
        return $this;
    }
}
