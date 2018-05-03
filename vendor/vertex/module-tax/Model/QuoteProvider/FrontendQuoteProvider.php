<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\QuoteProvider;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Provides the Quote currently being operated on in the Frontend area
 *
 * Used to enable Vertex Tax Calculation on multi-shipping orders
 */
class FrontendQuoteProvider implements QuoteProviderInterface
{
    /** @var Quote */
    private $quote;

    /** @var CheckoutSession */
    private $session;

    /**
     * @param CheckoutSession $session
     */
    public function __construct(CheckoutSession $session)
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
