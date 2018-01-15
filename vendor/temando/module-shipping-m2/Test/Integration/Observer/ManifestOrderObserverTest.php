<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;
use Temando\Shipping\Test\Integration\Fixture\PlacedOrderFixture;

/**
 * ManifestOrderObserverTest
 *
 * @package  Temando\Shipping\Test\Integration
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ManifestOrderObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Event\Invoker\InvokerDefault
     */
    private $invoker;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    private $observer;

    /**
     * delegate fixtures creation to separate class.
     */
    public static function createQuoteAndOrderFixture()
    {
        PlacedOrderFixture::createQuoteAndOrderFixture();
    }

    /**
     * delegate fixtures rollback to separate class.
     */
    public static function createQuoteAndOrderFixtureRollback()
    {
        PlacedOrderFixture::createQuoteAndOrderFixtureRollback();
    }

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->invoker = $this->objectManager->get(\Magento\Framework\Event\InvokerInterface::class);
        $this->observer = $this->objectManager->get(\Magento\Framework\Event\Observer::class);
    }

    /**
     * clean up
     */
    protected function tearDown()
    {
        $this->objectManager->removeSharedInstance(ManifestOrderObserver::class);

        parent::tearDown();
    }

    /**
     * Assert platform order and local reference being updated.
     *
     * @test
     * @magentoAppArea adminhtml
     * @magentoDataFixture createQuoteAndOrderFixture
     * @magentoConfigFixture default_store general/store_information/name Foo Name
     */
    public function orderIsUpdatedAtApi()
    {
        $this->markTestIncomplete('reference to external entity moved from quote to shipping address');

        $orderIncrementId = PlacedOrderFixture::getOrderIncrementId();

        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId);
        $searchCriteriaBuilder->setPageSize(1);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $searchResult */
        $orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $searchResult = $orderRepository->getList($searchCriteriaBuilder->create());
        /** @var \Magento\Sales\Model\Order $order */
        $order = $searchResult->getFirstItem();

        /** @var CartRepositoryInterface $quoteRepository */
        $quoteRepository = $this->objectManager->get(CartRepositoryInterface::class);
        $quote = $quoteRepository->get($order->getQuoteId());

        $config = [
            'instance' => ManifestOrderObserver::class,
            'name' => 'temando_manifest_order',
        ];

        $this->observer->setData([
            'order' => $order,
            'quote' => $quote,
        ]);
        $this->invoker->dispatch($config, $this->observer);
    }
}
