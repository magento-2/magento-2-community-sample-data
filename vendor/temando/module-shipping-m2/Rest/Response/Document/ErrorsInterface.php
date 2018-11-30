<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Document;

/**
 * Temando API Error Collection Document
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface ErrorsInterface
{
    /**
     * @return \Temando\Shipping\Rest\Response\DataObject\Error[]
     */
    public function getErrors();

    /**
     * @param \Temando\Shipping\Rest\Response\DataObject\Error[] $errors
     * @return void
     */
    public function setErrors(array $errors);
}
