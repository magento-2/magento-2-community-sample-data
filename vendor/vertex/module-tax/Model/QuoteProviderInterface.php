<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Quote\Model\Quote;

/**
 * Shared object for storing the {@see Quote} necessary for Tax Calculation
 */
interface QuoteProviderInterface
{
    /**
     * Set the Quote that will be used for all subsequent tax calculation calls
     *
     * @param Quote $quote
     * @return QuoteProviderInterface
     */
    public function setQuote(Quote $quote);

    /**
     * Get the Quote
     *
     * @return Quote|null
     */
    public function getQuote();
}
