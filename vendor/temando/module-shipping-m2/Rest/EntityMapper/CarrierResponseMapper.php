<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Model\CarrierInterface;
use Temando\Shipping\Model\CarrierInterfaceFactory;
use Temando\Shipping\Rest\Response\DataObject\CarrierConfiguration;
use Temando\Shipping\Rest\Response\DataObject\CarrierIntegration;

/**
 * Map API data to application data object
 *
 * @package Temando\Shipping\Rest
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class CarrierResponseMapper
{
    /**
     * @var CarrierInterfaceFactory
     */
    private $carrierFactory;

    /**
     * CarrierResponseMapper constructor.
     * @param CarrierInterfaceFactory $carrierFactory
     */
    public function __construct(CarrierInterfaceFactory $carrierFactory)
    {
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * @param CarrierIntegration $apiIntegration
     * @return string[]
     */
    private function getAvailableServices(CarrierIntegration $apiIntegration)
    {
        $apiIntegrationServices = $apiIntegration->getAttributes()->getServices();
        $serviceNames = [];

        foreach ($apiIntegrationServices as $apiIntegrationService) {
            $serviceNames[$apiIntegrationService->getId()] = $apiIntegrationService->getName();
        }

        return $serviceNames;
    }

    /**
     * @param CarrierConfiguration $apiConfiguration
     * @param CarrierIntegration $apiIntegration
     * @return CarrierInterface
     */
    public function map(
        CarrierConfiguration $apiConfiguration,
        CarrierIntegration $apiIntegration = null
    ) {
        /** @var \Temando\Shipping\Model\Carrier $carrier */
        $carrier = $this->carrierFactory->create(['data' => [
            CarrierInterface::CONFIGURATION_ID => (string)$apiConfiguration->getId(),
            CarrierInterface::INTEGRATION_ID => (string)$apiConfiguration->getAttributes()->getIntegrationId(),
            CarrierInterface::CONNECTION_NAME => (string)$apiConfiguration->getAttributes()->getConnectionName(),
            CarrierInterface::STATUS => (string)$apiConfiguration->getAttributes()->getStatus(),
        ]]);

        if ($apiIntegration) {
            $availableServices = $this->getAvailableServices($apiIntegration);
            $activeServiceIds = array_combine(
                $apiConfiguration->getAttributes()->getIntegrationServiceIds(),
                $apiConfiguration->getAttributes()->getIntegrationServiceIds()
            );
            $activeServices = array_intersect_key($availableServices, $activeServiceIds);

            $carrier->addData([
                CarrierInterface::NAME => (string)$apiIntegration->getAttributes()->getName(),
                CarrierInterface::LOGO => (string)$apiIntegration->getAttributes()->getLogo(),
                CarrierInterface::ACTIVE_SERVICES => $activeServices,
            ]);
        }

        return $carrier;
    }
}
