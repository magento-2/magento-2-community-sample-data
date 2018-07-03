<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Framework\App\ActionInterface;
use Vertex\Tax\Model\ErrorMessageDisplayState;

/**
 * Turn on error messages during Multishipping
 */
class MultishippingErrorMessageSupport
{
    /** @var ErrorMessageDisplayState */
    private $messageDisplayState;

    /**
     * @param ErrorMessageDisplayState $messageDisplayState
     */
    public function __construct(ErrorMessageDisplayState $messageDisplayState)
    {
        $this->messageDisplayState = $messageDisplayState;
    }

    /**
     * Turn on error messages
     *
     * @param ActionInterface $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject required for interceptor
     */
    public function beforeExecute(ActionInterface $subject)
    {
        $this->messageDisplayState->enable();
    }
}
