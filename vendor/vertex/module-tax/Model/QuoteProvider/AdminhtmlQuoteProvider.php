<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\QuoteProvider;

use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\QuoteProviderInterface;
use Magento\Backend\Model\Session\Quote as BackendSession;

/**
 * Provides the quote currently being operated on in the admin area
 *
 * Used to enable Vertex Tax Calculation on Admin-placed orders
 */
class AdminhtmlQuoteProvider implements QuoteProviderInterface
{
    /** @var Quote */
    private $quote;

    /** @var BackendSession */
    private $session;

    /**
     * @param BackendSession $session
     */
    public function __construct(BackendSession $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            $this->setQuote($this->session->getQuote());
        }
        return $this->quote;
    }
}
