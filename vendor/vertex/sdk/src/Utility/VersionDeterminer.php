<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility;

use Vertex\Exception\ConfigurationException;

/**
 * Decides which API level to use
 *
 * @api
 */
class VersionDeterminer
{
    /**
     * Determine the API-level to use for a given URL
     *
     * @param string $url
     * @return string
     * @throws ConfigurationException
     */
    public function execute($url)
    {
        $pieces = parse_url($url);
        if (substr($pieces['path'], -2) === '60') {
            return '60';
        }
        if (substr($pieces['path'], -2) === '70') {
            return '70';
        }

        throw new ConfigurationException('Provided URL does not contain a known, supported version');
    }
}
