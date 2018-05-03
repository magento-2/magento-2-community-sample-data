<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Request;

use Vertex\Tax\Model\Config;

/**
 * Header formatter for Vertex API Calls
 */
class Header
{
    /** @var Config */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Create properly formatted header data for a Vertex API call
     *
     * @return array
     */
    public function getFormattedHeaderData()
    {
        $data = [];
        $data['Login'] = [
            'TrustedId' => $this->config->getTrustedId()
        ];

        return $data;
    }
}
