<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\QuoteProvider;

use Magento\Quote\Model\Quote;
use Vertex\Tax\Model\QuoteProviderInterface;

/**
 * Generic Provider of Quotes
 *
 * Used to act as shared storage in areas that do not have an area-specific implementation
 */
class GenericQuoteProvider implements QuoteProviderInterface
{
    /** @var Quote */
    private $quote;

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
        return $this->quote;
    }
}
