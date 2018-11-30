<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Cache;

use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\App\Cache\Type\FrontendPool;

/**
 * Cache type declaration for persistent storage of Vertex data.
 */
class Type extends TagScope
{
    const TYPE_IDENTIFIER = 'vertex';
    const CACHE_TAG = 'VERTEX';

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
