<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Vertex\Tax\Model\Calculation\VertexCalculator;
use Magento\Framework\Registry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Retrieve tax calculation service errors.
 *
 * Prepares error messages and sends them to the message manager as a warning.
 */
class QuoteCollectTotalsAfterObserver implements ObserverInterface
{
    /** @var ManagerInterface */
    private $messageManager;

    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Registry $registry,
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
        $this->registry = $registry;
    }

    /**
     * Get the last error message from a Vertex calculation.
     *
     * @return string|null
     */
    private function getError()
    {
        return $this->registry->registry(VertexCalculator::VERTEX_CALCULATION_ERROR);
    }

    /**
     * Add service error message to session storage.
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Only event occurrence is needed.
     */
    public function execute(Observer $observer)
    {
        $error = $this->getError();

        if ($error !== null) {
            $this->messageManager->addWarningMessage($error);
        }
    }
}
