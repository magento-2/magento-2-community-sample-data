<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Vertex\Tax\Api\Data\LogEntryInterface;
use Vertex\Tax\Api\Data\LogEntrySearchResultsInterface;
use Vertex\Tax\Api\Data\LogEntrySearchResultsInterfaceFactory;
use Vertex\Tax\Api\LogEntryRepositoryInterface;
use Vertex\Tax\Model\CollectionProcessor;
use Vertex\Tax\Model\Data\LogEntry;
use Vertex\Tax\Model\Data\LogEntryFactory;
use Vertex\Tax\Model\ResourceModel\LogEntry as ResourceModel;
use Vertex\Tax\Model\ResourceModel\LogEntry\Collection;
use Vertex\Tax\Model\ResourceModel\LogEntry\CollectionFactory;

/**
 * Repository of Log Entries
 */
class LogEntryRepository implements LogEntryRepositoryInterface
{
    /** @var ResourceModel */
    private $resourceModel;

    /** @var LogEntryFactory */
    private $logEntryFactory;

    /** @var CollectionFactory */
    private $collectionFactory;

    /** @var LogEntrySearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /** @var CollectionProcessor */
    private $collectionProcessor;

    /**
     * @param ResourceModel $resourceModel
     * @param LogEntryFactory $logEntryFactory
     * @param CollectionFactory $collectionFactory
     * @param LogEntrySearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessor $collectionProcessor
     */
    public function __construct(
        ResourceModel $resourceModel,
        LogEntryFactory $logEntryFactory,
        CollectionFactory $collectionFactory,
        LogEntrySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessor $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->logEntryFactory = $logEntryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(LogEntryInterface $logEntry)
    {
        $model = $this->mapDataIntoModel($logEntry);
        try {
            $this->resourceModel->save($model);
        } catch (\Exception $originalException) {
            throw new CouldNotSaveException(__('Could not save Log Entry'), $originalException);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var LogEntrySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults->setTotalCount($collection->getSize());

        $logEntries = [];
        /** @var LogEntry $logEntryModel */
        foreach ($collection as $logEntryModel) {
            $logEntries[] = $logEntryModel;
        }
        $searchResults->setItems($logEntries);
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(LogEntryInterface $logEntry)
    {
        return $this->deleteById($logEntry->getId());
    }

    /**
     * @inheritdoc
     */
    public function deleteById($logEntryId)
    {
        /** @var LogEntry $model */
        $model = $this->logEntryFactory->create();
        $model->setId($logEntryId);
        try {
            $this->resourceModel->delete($model);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete log entry'), $e);
        }

        return true;
    }

    /**
     * Convert a LogEntryInterface into a LogEntry model
     *
     * @param LogEntryInterface $logEntry
     * @return LogEntry
     */
    private function mapDataIntoModel(LogEntryInterface $logEntry)
    {
        /** @var LogEntry $model */
        $model = $this->logEntryFactory->create();
        $model->setData(
            [
                LogEntryInterface::FIELD_ID => $logEntry->getId(),
                LogEntryInterface::FIELD_TYPE => $logEntry->getType(),
                LogEntryInterface::FIELD_CART_ID => $logEntry->getCartId(),
                LogEntryInterface::FIELD_ORDER_ID => $logEntry->getOrderId(),
                LogEntryInterface::FIELD_TOTAL_TAX => $logEntry->getTotalTax(),
                LogEntryInterface::FIELD_SOURCE_PATH => $logEntry->getSourcePath(),
                LogEntryInterface::FIELD_TAX_AREA_ID => $logEntry->getTaxAreaId(),
                LogEntryInterface::FIELD_SUBTOTAL => $logEntry->getSubTotal(),
                LogEntryInterface::FIELD_TOTAL => $logEntry->getTotal(),
                LogEntryInterface::FIELD_LOOKUP_RESULT => $logEntry->getLookupResult(),
                LogEntryInterface::FIELD_REQUEST_DATE => $logEntry->getDate(),
                LogEntryInterface::FIELD_REQUEST_XML => $logEntry->getRequestXml(),
                LogEntryInterface::FIELD_RESPONSE_XML => $logEntry->getResponseXml(),
            ]
        );
        return $model;
    }
}
