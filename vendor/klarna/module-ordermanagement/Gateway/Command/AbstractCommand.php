<?php
/**
 * This file is part of the Klarna Order Management module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Ordermanagement\Gateway\Command;

use Klarna\Core\Helper\ConfigHelper;
use Klarna\Core\Helper\KlarnaConfig;
use Klarna\Core\Model\OrderRepository as KlarnaOrderRepository;
use Klarna\Ordermanagement\Model\Api\Factory;
use Klarna\Ordermanagement\Model\Api\Ordermanagement;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository as MageOrderRepository;

/**
 * Class AbstractCommand
 *
 * @package Klarna\Ordermanagement\Gateway\Command
 */
abstract class AbstractCommand extends DataObject implements CommandInterface
{
    /**
     * @var KlarnaOrderRepository
     */
    public $klarnaOrderRepository;

    /**
     * @var Ordermanagement
     */
    private $om;

    /**
     * @var array
     */
    public $omCache = [];

    /**
     * @var MageQuoteRepository
     */
    public $mageQuoteRepository;

    /**
     * @var MageOrderRepository
     */
    public $mageOrderRepository;

    /**
     * @var KlarnaConfig
     */
    public $helper;

    /**
     * @var Factory
     */
    public $omFactory;

    /**
     * @var MessageManager
     */
    public $messageManager;

    /**
     * AbstractCommand constructor.
     *
     * @param KlarnaOrderRepository $klarnaOrderRepository
     * @param MageQuoteRepository   $mageQuoteRepository
     * @param MageOrderRepository   $mageOrderRepository
     * @param KlarnaConfig          $helper
     * @param Factory               $omFactory
     * @param MessageManager        $messageManager
     * @param array                 $data
     */
    public function __construct(
        KlarnaOrderRepository $klarnaOrderRepository,
        MageQuoteRepository $mageQuoteRepository,
        MageOrderRepository $mageOrderRepository,
        KlarnaConfig $helper,
        Factory $omFactory,
        MessageManager $messageManager,
        array $data = []
    ) {
        parent::__construct($data);
        $this->klarnaOrderRepository = $klarnaOrderRepository;
        $this->mageQuoteRepository = $mageQuoteRepository;
        $this->mageOrderRepository = $mageOrderRepository;
        $this->helper = $helper;
        $this->omFactory = $omFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * AbstractCommand command
     *
     * @param array $commandSubject
     *
     * @return null|Command\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    abstract public function execute(array $commandSubject);

    /**
     * Get a Klarna order
     *
     * @param $order
     *
     * @return \Klarna\Core\Model\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getKlarnaOrder($order)
    {
        return $this->klarnaOrderRepository->getByOrder($order);
    }

    /**
     * Get api class
     *
     * @param OrderInterface $order
     * @return Ordermanagement
     * @internal param Store $store
     */
    public function getOmApi(OrderInterface $order)
    {
        $store = $order->getStore();
        if (isset($this->omCache[$store->getId()])) {
            $this->om = $this->omCache[$store->getId()];
            return $this->om;
        }
        $omClass = $this->helper->getOrderMangagementClass($store);
        $this->om = $this->omFactory->create($omClass);
        $this->om->resetForStore($store, $order->getPayment()->getMethod());
        $this->omCache[$store->getId()] = $this->om;

        return $this->om;
    }

    /**
     * Extending the error message with information from the api response
     *
     * @param DataObject $response
     * @param string $errorMessage
     * @param string $type
     * @return \Magento\Framework\Phrase
     */
    public function getFullErrorMessage($response, $errorMessage, $type)
    {
        $apiMessage = implode($response->getErrorMessages(), '<br/>');
        if (!empty($apiMessage)) {
            $errorMessage = __(
                '%1 Klarna %2 api error messages: %3',
                "$errorMessage<br/><br/>",
                $type,
                '<br/><i>' . $apiMessage . '</i>'
            );
        }

        return $errorMessage;
    }
}
