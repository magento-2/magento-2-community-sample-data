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
class Errors implements ErrorsInterface
{
    /**
     * @var \Temando\Shipping\Rest\Response\DataObject\Error[]
     */
    private $errors = [];

    /**
     * Obtain error entities
     *
     * @return \Temando\Shipping\Rest\Response\DataObject\Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set error entities
     *
     * @param $errors \Temando\Shipping\Rest\Response\DataObject\Error[]
     * @return void
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
