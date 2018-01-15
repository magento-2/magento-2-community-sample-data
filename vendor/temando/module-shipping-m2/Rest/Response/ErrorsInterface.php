<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response;

/**
 * Temando API Errors
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface ErrorsInterface
{
    /**
     * Obtain error entities
     *
     * @return \Temando\Shipping\Rest\Response\Type\ErrorResponseType[]
     */
    public function getErrors();

    /**
     * Set error entities
     *
     * @param \Temando\Shipping\Rest\Response\Type\ErrorResponseType[] $errors
     * @return void
     */
    public function setErrors(array $errors);
}
