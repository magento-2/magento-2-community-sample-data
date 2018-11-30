<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Rest\Response\Document;

use Temando\Shipping\Rest\Response\DataObject\Batch;

/**
 * Temando API Get Batch Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface GetBatchInterface extends CompoundDocumentInterface
{
    /**
     * Obtain response entities
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Batch
     */
    public function getData();

    /**
     * Set response entities
     *
     * @param \Temando\Shipping\Rest\Response\DataObject\Batch $batch
     * @return void
     */
    public function setData(Batch $batch);
}
