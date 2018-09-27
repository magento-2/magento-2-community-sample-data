<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\UrlInterface;
use Temando\Shipping\Model\CarrierInterface;

/**
 * Carrier URL provider
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class CarrierUrl implements EntityUrlInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CarrierUrl constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getNewActionUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/configuration_carrier/new');
    }

    /**
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/configuration_carrier/index');
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getViewActionUrl(array $data): string
    {
        return '';
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getEditActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('temando/configuration_carrier/edit', [
            CarrierInterface::CONFIGURATION_ID => $data[CarrierInterface::CONFIGURATION_ID],
            CarrierInterface::INTEGRATION_ID => $data[CarrierInterface::INTEGRATION_ID]
        ]);
    }

    /**
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getDeleteActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('temando/configuration_carrier/delete', [
            CarrierInterface::CONFIGURATION_ID => $data[CarrierInterface::CONFIGURATION_ID]
        ]);
    }

    /**
     * Obtain Add New Carrier url.
     *
     * @return string
     */
    public function getCarrierRegistrationPageUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/configuration_carrier/register');
    }
}
