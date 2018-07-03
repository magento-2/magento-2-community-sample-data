<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\EventStream;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Temando\Shipping\Rest\Adapter\EventStreamApiInterface;
use Temando\Shipping\Rest\Exception\AdapterException;
use Temando\Shipping\Rest\Request\ItemRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\StreamCreateRequestInterfaceFactory;
use Temando\Shipping\Rest\Request\Type\StreamRequestTypeFactory;

/**
 * Temando Stream Repository
 *
 * @package  Temando\Shipping\Model
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class StreamRepository implements StreamRepositoryInterface
{
    /**
     * @var EventStreamApiInterface
     */
    private $apiAdapter;

    /**
     * @var StreamCreateRequestInterfaceFactory
     */
    private $streamCreateRequestFactory;

    /**
     * @var StreamRequestTypeFactory
     */
    private $streamRequestTypeFactory;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * StreamRepository constructor.
     *
     * @param EventStreamApiInterface $apiAdapter
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param StreamRequestTypeFactory $streamRequuestTypeFactory
     * @param StreamCreateRequestInterfaceFactory $streamCreateRequestFactory
     */
    public function __construct(
        EventStreamApiInterface $apiAdapter,
        ItemRequestInterfaceFactory $itemRequestFactory,
        StreamRequestTypeFactory $streamRequuestTypeFactory,
        StreamCreateRequestInterfaceFactory $streamCreateRequestFactory
    ) {
        $this->apiAdapter         = $apiAdapter;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->streamRequestTypeFactory = $streamRequuestTypeFactory;
        $this->streamCreateRequestFactory = $streamCreateRequestFactory;
    }

    /**
     * @param string $streamId
     * @return void
     * @throws CouldNotSaveException
     */
    public function save($streamId)
    {
        try {
            $stream = $this->streamRequestTypeFactory->create(['streamId' => $streamId]);
            $request = $this->streamCreateRequestFactory->create(['stream' => $stream]);
            $this->apiAdapter->createStream($request);
        } catch (AdapterException $e) {
            throw new CouldNotSaveException(__('Unable to save event stream.'), $e);
        }
    }

    /**
     * @param string $streamId
     * @return void
     * @throws CouldNotDeleteException
     */
    public function delete($streamId)
    {
        try {
            $request = $this->itemRequestFactory->create(['entityId' => $streamId]);
            $this->apiAdapter->deleteStream($request);
        } catch (AdapterException $e) {
            throw new CouldNotDeleteException(__('Unable to delete event stream.'), $e);
        }
    }
}
