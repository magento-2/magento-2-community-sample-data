<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxArea;

use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Psr\Log\LoggerInterface;

/**
 * Response object for Tax Area Requests
 */
class TaxAreaResponse
{
    /** @var CollectionFactory */
    private $dataCollectionFactory;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    /** @var string */
    private $requestCity;

    /** @var array */
    private $taxAreaResults;

    /** @var Collection */
    private $taxAreaLocations;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param CollectionFactory $dataCollectionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $dataCollectionFactory,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger
    ) {
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * Parse a Tax Area Requests's response and store it's data in this object
     *
     * @param array $responseArray
     * @param array $requestData
     * @return $this
     */
    public function parseResponse(array $responseArray, array $requestData)
    {
        if (isset($requestData['TaxAreaRequest']['TaxAreaLookup']['PostalAddress']['City'])) {
            $this->setRequestCity($requestData['TaxAreaRequest']['TaxAreaLookup']['PostalAddress']['City']);
        }
        $this->setTaxAreaResults($responseArray);

        return $this;
    }

    /**
     * Get the first returned tax area information
     *
     * @return \Magento\Framework\DataObject
     */
    public function getFirstTaxAreaInfo()
    {
        $collection = $this->getTaxAreaLocationsCollection();
        return $collection->load(1)->getFirstItem();
    }

    /**
     * Get all the tax areas returned by the API
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getTaxAreaLocationsCollection()
    {
        if (!$this->taxAreaLocations) {
            /** @var Collection $taxAreaInfoCollection */
            $taxAreaInfoCollection = $this->dataCollectionFactory->create();

            if (!$this->getTaxAreaResults()) {
                return $taxAreaInfoCollection;
            }

            $taxAreaResults = $this->getTaxAreaResults();
            if (isset($taxAreaResults['TaxAreaResult']) && !isset($taxAreaResults['TaxAreaResult']['taxAreaId'])) {
                $taxAreaResults = $taxAreaResults['TaxAreaResult'];
            }

            foreach ($taxAreaResults as $taxResponse) {
                if (!isset(
                    $taxResponse['taxAreaId'],
                    $taxResponse['Jurisdiction'],
                    $taxResponse['confidenceIndicator']
                )) {
                    continue;
                }
                $taxJurisdictions = $taxResponse['Jurisdiction'];
                krsort($taxJurisdictions);
                $areaNames = [];
                foreach ($taxJurisdictions as $areaJurisdiction) {
                    if (!isset($areaJurisdiction['_'])) {
                        continue;
                    }
                    $areaNames[] = $areaJurisdiction['_'];
                }
                $areaName = ucwords(strtolower(implode(', ', $areaNames)));

                /** @var DataObject $taxAreaInfo */
                $taxAreaInfo = $this->dataObjectFactory->create();
                $taxAreaInfo->setData('area_name', $areaName);
                $taxAreaInfo->setData('tax_area_id', $taxResponse['taxAreaId']);
                $taxAreaInfo->setData('confidence_indicator', $taxResponse['confidenceIndicator']);
                if (isset($taxResponse['PostalAddress'])) {
                    $taxAreaInfo->setData('tax_area_city', $taxResponse['PostalAddress']);
                } else {
                    $taxAreaInfo->setData('tax_area_city', $this->getRequestCity());
                }

                $taxAreaInfo->setData('request_city', $this->getRequestCity());
                try {
                    $taxAreaInfoCollection->addItem($taxAreaInfo);
                } catch (\Exception $e) {
                    $this->logger->error(
                        $e->getMessage() . PHP_EOL . $e->getTraceAsString()
                    );
                }
            }
            $this->taxAreaLocations = $taxAreaInfoCollection;
        }
        return $this->taxAreaLocations;
    }

    /**
     * Get the Tax Area that had the highest confidence indicator
     *
     * @return \Magento\Framework\DataObject|null
     */
    public function getTaxAreaWithHighestConfidence()
    {
        $taxAreaCollection = $this->getTaxAreaLocationsCollection();
        $confidence = 0;
        $selectedTaxArea = null;
        foreach ($taxAreaCollection as $taxAreaInfo) {
            if ($taxAreaInfo->getConfidenceIndicator() > $confidence) {
                $confidence = $taxAreaInfo->getConfidenceIndicator();
                $selectedTaxArea = $taxAreaInfo;
            }
        }

        return $selectedTaxArea;
    }

    /**
     * Get the City made on the Request
     *
     * @return string
     */
    public function getRequestCity()
    {
        return $this->requestCity;
    }

    /**
     * Set the City made on the Request
     *
     * @param string $requestCity
     * @return TaxAreaResponse
     */
    public function setRequestCity($requestCity)
    {
        $this->requestCity = $requestCity;
        return $this;
    }

    /**
     * Get the Results from the Request
     *
     * @return array
     */
    public function getTaxAreaResults()
    {
        return $this->taxAreaResults;
    }

    /**
     * Set the Results form the Request
     *
     * @param array $taxAreaResults
     * @return TaxAreaResponse
     */
    public function setTaxAreaResults($taxAreaResults)
    {
        $this->taxAreaResults = $taxAreaResults;
        return $this;
    }
}
