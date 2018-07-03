<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest;

/**
 * Temando Rest HTTP Client Adapter
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface RestClientInterface
{
    /**
     * @param string $uri
     * @param string $rawBody
     * @param string[] $headers
     * @return string
     */
    public function post($uri, $rawBody, array $headers);

    /**
     * @param string $uri
     * @param string $rawBody
     * @param string[] $headers
     * @return string
     */
    public function put($uri, $rawBody, array $headers);

    /**
     * @param string   $uri
     * @param string[] $queryParams
     * @param string[] $headers
     *
     * @return string
     */
    public function get($uri, array $queryParams, array $headers);

    /**
     * @param string   $uri
     * @param string[] $headers
     *
     * @return mixed
     */
    public function delete($uri, array $headers);
}
