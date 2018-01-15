<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\ResourceModel\Packaging;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Temando\Shipping\Model\PackagingInterface;
use Temando\Shipping\Model\ResourceModel\Repository\PackagingRepositoryInterface;
use Temando\Shipping\Model\ResourceModel\Webservice\Collection as ApiCollection;

/**
 * Temando Packaging Resource Collection
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Collection extends ApiCollection
{
    /**
     * @var PackagingRepositoryInterface
     */
    private $packagingRepository;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param ManagerInterface $messageManager
     * @param PackagingRepositoryInterface $packagingRepository
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ManagerInterface $messageManager,
        PackagingRepositoryInterface $packagingRepository
    ) {
        $this->packagingRepository = $packagingRepository;

        parent::__construct($entityFactory, $messageManager);
    }

    /**
     * @return PackagingInterface[]
     */
    public function fetchData()
    {
        $containers = $this->packagingRepository->getList();
        return $containers;
    }
}
