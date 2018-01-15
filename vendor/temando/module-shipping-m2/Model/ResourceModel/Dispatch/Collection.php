<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Dispatch;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\DispatchInterface;
use Temando\Shipping\Model\ResourceModel\Repository\DispatchRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Dispatch Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var DispatchRepositoryInterface
     */
    private $dispatchRepository;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param DispatchRepositoryInterface $dispatchRepository
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        DispatchRepositoryInterface $dispatchRepository
    ) {
        $this->dispatchRepository = $dispatchRepository;

        parent::__construct($entityFactory, $messageManager);
    }

    /**
     * Perform API call
     * @return DispatchInterface[]
     */
    public function fetchData()
    {
        $dispatches = $this->dispatchRepository->getList();
        return $dispatches;
    }
}
