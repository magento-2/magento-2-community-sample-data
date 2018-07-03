<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Get Item Operation
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ItemRequest implements ItemRequestInterface
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * ItemRequest constructor.
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string[]
     */
    public function getPathParams()
    {
        return [
            $this->entityId,
        ];
    }
}
