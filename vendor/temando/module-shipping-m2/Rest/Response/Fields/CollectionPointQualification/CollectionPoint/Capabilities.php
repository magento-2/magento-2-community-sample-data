<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint;

/**
 * Temando API Collection Point Qualification Collection Point Capabilities Field
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class Capabilities
{
    /**
     * @codingStandardsIgnoreLine
     * @var \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities\AuthorityToLeave
     */
    private $authorityToLeave;

    /**
     * @codingStandardsIgnoreLine
     * @return \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities\AuthorityToLeave
     */
    public function getAuthorityToLeave()
    {
        return $this->authorityToLeave;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Fields\CollectionPointQualification\CollectionPoint\Capabilities\AuthorityToLeave $authorityToLeave
     * @return void
     */
    public function setAuthorityToLeave($authorityToLeave)
    {
        $this->authorityToLeave = $authorityToLeave;
    }
}
