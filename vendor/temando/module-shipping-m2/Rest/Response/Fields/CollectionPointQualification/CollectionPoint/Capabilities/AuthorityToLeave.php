<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities;

/**
 * Temando API Collection Point Qualification Collection Point Capabilities Authority To Leave Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class AuthorityToLeave
{
    /**
     * @var bool
     */
    private $required;

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     * @return void
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }
}
