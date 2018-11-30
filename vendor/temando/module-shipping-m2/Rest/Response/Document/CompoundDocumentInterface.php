<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Compound Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface CompoundDocumentInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\AbstractResource[]
     */
    public function getIncluded();

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\AbstractResource[] $included
     * @return void
     */
    public function setIncluded(array $included);
}
