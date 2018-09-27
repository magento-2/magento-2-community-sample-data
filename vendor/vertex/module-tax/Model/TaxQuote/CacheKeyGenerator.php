<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\TaxQuote;

/**
 * Generates a cache storage key for a Quotation Request
 */
class CacheKeyGenerator
{
    /**
     * Convert a Tax Quote Request into a string for caching
     *
     * @param array $request
     * @return string
     */
    public function generateCacheKey(array $request)
    {
        return sha1(json_encode($request));
    }
}
