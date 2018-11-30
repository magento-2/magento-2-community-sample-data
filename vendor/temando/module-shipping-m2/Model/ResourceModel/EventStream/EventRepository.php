<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\EventStream;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\StreamEventInterface;
use Temando\Shipping\Rest\EntityMapper\StreamEventResponseMapper;
use Temando\Shipping\Rest\Adapter\EventStreamApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\StreamEventItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\StreamEventListRequestInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\StreamEventResponseType;

/**
 * Temando Event Stream Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class EventRepository implements EventRepositoryInterface
{
    /**
     * @var EventStreamApiInterface
     */
    private $apiAdapter;

    /**
     * @var StreamEventListRequestInterfaceFactory
     */
    private $eventListRequestFactory;

    /**
     * @var StreamEventItemRequestInterfaceFactory
     */
    private $eventItemRequestFactory;

    /**
     * @var StreamEventResponseMapper
     */
    private $streamEventMapper;

    /**
     * StreamEventRepository constructor.
     *
     * @param EventStreamApiInterface $apiAdapter
     * @param StreamEventItemRequestInterfaceFactory $eventItemRequestFactory
     * @param StreamEventListRequestInterfaceFactory $listRequestFactory
     * @param StreamEventResponseMapper $streamEventMapper
     */
    public function __construct(
        EventStreamApiInterface $apiAdapter,
        StreamEventItemRequestInterfaceFactory $eventItemRequestFactory,
        StreamEventListRequestInterfaceFactory $listRequestFactory,
        StreamEventResponseMapper $streamEventMapper
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->eventItemRequestFactory = $eventItemRequestFactory;
        $this->eventListRequestFactory = $listRequestFactory;
        $this->streamEventMapper = $streamEventMapper;
    }

    /**
     * @param string $streamId
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return \Temando\Shipping\Model\StreamEventInterface[]
     * @throws LocalizedException
     */
    public function getEventList($streamId, $offset = null, $limit = null)
    {
        try {
            $request = $this->eventListRequestFactory->create([
                'streamId' => $streamId,
                'offset' => $offset,
                'limit'  => $limit,
            ]);

            // convert api response to local (reduced) event objects
            $apiStreamEvents = $this->apiAdapter->getStreamEvents($request);
            $streamEvents = array_map(function (StreamEventResponseType $apiEvent) {
                return $this->streamEventMapper->map($apiEvent);
            }, $apiStreamEvents);
        } catch (AdapterException $e) {
            throw new LocalizedException(__('Unable to load stream events.'), $e);
        }

        return $streamEvents;
    }

    /**
     * @param string $streamId
     * @param string $eventId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($streamId, $eventId)
    {
        try {
            $request = $this->eventItemRequestFactory->create([
                'streamId' => $streamId,
                'entityId' => $eventId,
            ]);
            $this->apiAdapter->deleteStreamEvent($request);
        } catch (AdapterException $e) {
            throw new CouldNotDeleteException(__('Unable to delete stream event.'), $e);
        }
    }
}
