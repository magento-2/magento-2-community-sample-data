<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Block\Adminhtml\Configuration;

use Magento\Backend\Block\Widget\Context as WidgetContext;
use Magento\Backend\Model\Auth\StorageInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Integration\Model\Oauth\Token;
use Magento\Security\Model\Config;
use Temando\Shipping\Block\Adminhtml\Template\AbstractComponent;
use Temando\Shipping\Model\Config\ModuleConfigInterface;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando Checkout Fields Component Block
 *
 * @package  Temando\Shipping\Block
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 *
 * @api
 * @deprecated since 1.0.5 | Block data is provided by view model
 * @see \Temando\Shipping\ViewModel\Config\CheckoutFields
 */
class CheckoutFieldsComponent extends AbstractComponent
{
    /**
     * @var ModuleConfigInterface
     */
    private $moduleConfig;

    /**
     * CheckoutFieldsComponent constructor.
     * @param WidgetContext $context
     * @param WsConfigInterface $config
     * @param StorageInterface $session
     * @param AuthenticationInterface $auth
     * @param Token $token
     * @param DateTime $dateTime
     * @param RemoteAddress $remoteAddress
     * @param Config $securityConfig
     * @param ModuleConfigInterface $moduleConfig
     * @param mixed[] $data
     */
    public function __construct(
        WidgetContext $context,
        WsConfigInterface $config,
        StorageInterface $session,
        AuthenticationInterface $auth,
        Token $token,
        DateTime $dateTime,
        RemoteAddress $remoteAddress,
        Config $securityConfig,
        ModuleConfigInterface $moduleConfig,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;

        parent::__construct(
            $context,
            $config,
            $session,
            $auth,
            $token,
            $dateTime,
            $remoteAddress,
            $securityConfig,
            $data
        );
    }

    /**
     * Add Back Button.
     *
     * @return \Magento\Backend\Block\Widget\Container
     */
    protected function _prepareLayout()
    {
        $buttonData = [
            'label' => __('Back'),
            'onclick' => sprintf("window.location.href = '%s';", $this->getConfigurationPageUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->buttonList->add('back', $buttonData);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getUpdateCheckoutFieldEndpoint()
    {
        return $this->getUrl('temando/settings_checkout/save');
    }

    /**
     * @return string
     */
    public function getConfigurationPageUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit', [
            'section' => 'carriers',
            '_fragment' => 'carriers_temando-link',
        ]);
    }

    /**
     * @return string
     */
    public function getCheckoutFieldsData()
    {
        return $this->moduleConfig->getCheckoutFieldsDefinition();
    }
}
