<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Response\Type\Generic;

/**
 * Temando API Include/Exclude Response Type
 *
 * @package  Temando\Shipping\Rest
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Set
{
    /**
     * @var string[]
     */
    private $include = [];

    /**
     * @var string[]
     */
    private $exclude = [];

    /**
     * @return string[]
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * @param string[] $include
     * @return void
     */
    public function setInclude(array $include)
    {
        $this->include = $include;
    }

    /**
     * @return string[]
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param string[] $exclude
     * @return void
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;
    }
}
