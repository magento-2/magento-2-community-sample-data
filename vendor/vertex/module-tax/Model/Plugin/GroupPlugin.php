<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Config\Model\Config\Structure\Element\Group;
use Vertex\Tax\Model\ModuleManager;

/**
 * Hides likely unused tax classes from the store configuration
 *
 * @see Group
 */
class GroupPlugin
{
    /** @var ModuleManager */
    private $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Hides likely unused tax classes
     *
     * MEQP2 Warning: Unused Parameter $subject necessary for plugins
     *
     * @see Group::setData()
     *
     * @param Group $subject
     * @param \Closure $proceed
     * @param array $data
     * @param string $scope
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $subject is a necessary part of a plugin
     */
    public function aroundSetData(Group $subject, \Closure $proceed, $data, $scope)
    {
        $taxClasses = isset($data['path'], $data['id']) && $data['path'] === 'tax' && $data['id'] === 'classes';
        if ($taxClasses && !$this->moduleManager->isEnabled('Magento_GiftWrapping')) {
            $this->hide(
                $data,
                [
                    'giftwrap_order_class',
                    'giftwrap_order_code',
                    'giftwrap_item_class',
                    'giftwrap_item_code',
                    'printed_giftcard_class',
                    'printed_giftcard_code',
                ]
            );
        }

        if ($taxClasses && !$this->moduleManager->isEnabled('Magento_Reward')) {
            $this->hide(
                $data,
                [
                    'reward_points_class',
                    'reward_points_code',
                ]
            );
        }

        return $proceed($data, $scope);
    }

    /**
     * Updates the data array to hide a path
     *
     * @param array &$data
     * @param array $toHide
     */
    private function hide(array &$data, array $toHide)
    {
        if (isset($data['path'], $data['id']) && $data['path'] === 'tax' && $data['id'] === 'classes') {
            foreach ($toHide as $code) {
                if (is_array($data['children'][$code])) {
                    $data['children'][$code]['showInDefault'] = 0;
                    $data['children'][$code]['showInWebsite'] = 0;
                    $data['children'][$code]['showInStore'] = 0;
                }
            }
        }
    }
}
