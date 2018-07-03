<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint;

/**
 * Temando API Collection Point Capabilities Attributes Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Capabilities
{
    /**
     * @var \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities\AuthorityToLeave
     */
    private $authorityToLeave;

    /**
     * @return \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities\AuthorityToLeave
     */
    public function getAuthorityToLeave()
    {
        return $this->authorityToLeave;
    }

    /**
     * @codingStandardsIgnoreLine
     * @param \Temando\Shipping\Rest\Response\Type\CollectionPoints\Included\Attributes\CollectionPoint\Capabilities\AuthorityToLeave $authorityToLeave
     */
    public function setAuthorityToLeave($authorityToLeave)
    {
        $this->authorityToLeave = $authorityToLeave;
    }
}
