<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Api;

/**
 * Klarna order line abstract
 */
interface OrderLineInterface
{
    /**
     * Check if the order line is for an order item or a total collector
     *
     * @return boolean
     */
    public function isIsTotalCollector();

    /**
     * Retrieve code name
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code name
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code);

    /**
     * Collect process.
     *
     * @param BuilderInterface $object
     *
     * @return $this
     */
    public function collect(BuilderInterface $object);

    /**
     * Fetch
     *
     * @param BuilderInterface $object
     *
     * @return $this
     */
    public function fetch(BuilderInterface $object);
}
