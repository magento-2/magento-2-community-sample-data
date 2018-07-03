<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Observer;

use Vertex\Tax\Model\Calculation\VertexCalculator;
use Vertex\Tax\Model\TaxRegistry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Vertex\Tax\Model\ErrorMessageDisplayState;

/**
 * Retrieve tax calculation service errors.
 *
 * Prepares error messages and sends them to the message manager as a warning.
 */
class QuoteCollectTotalsAfterObserver implements ObserverInterface
{
    /** @var ManagerInterface */
    private $messageManager;

    /** @var TaxRegistry */
    private $taxRegistry;

    /** @var ErrorMessageDisplayState */
    private $messageDisplayState;

    /**
     * @param TaxRegistry $taxRegistry
     * @param ManagerInterface $messageManager
     * @param ErrorMessageDisplayState $messageDisplayState
     */
    public function __construct(
        TaxRegistry $taxRegistry,
        ManagerInterface $messageManager,
        ErrorMessageDisplayState $messageDisplayState
    ) {
        $this->messageManager = $messageManager;
        $this->taxRegistry = $taxRegistry;
        $this->messageDisplayState = $messageDisplayState;
    }

    /**
     * Get the last error message from a Vertex calculation.
     *
     * @return string|null
     */
    private function getError()
    {
        return $this->taxRegistry->lookup(TaxRegistry::KEY_ERROR_GENERIC);
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

        if ($error !== null && $this->messageDisplayState->isEnabled()) {
            $this->messageManager->addWarningMessage($error);
        }
    }
}
