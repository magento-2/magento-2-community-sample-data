<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Authentication;

use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Temando\Shipping\Rest\AuthenticationInterface;
use Temando\Shipping\Webservice\Config\WsConfigInterface;

/**
 * Temando Session Token Action
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Token extends BackendAction
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales';

    /**
     * @var WsConfigInterface
     */
    private $config;

    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @param Context $context
     * @param WsConfigInterface $config
     * @param AuthenticationInterface $auth
     */
    public function __construct(
        Context $context,
        WsConfigInterface $config,
        AuthenticationInterface $auth
    ) {
        $this->config = $config;
        $this->auth = $auth;

        parent::__construct($context);
    }

    /**
     * Only grant access for GET requests coming in via Javascript XMLHttpRequest.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        /** @var Http $request */
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest()) {
            return false;
        }

        return parent::_isAllowed();
    }

    /**
     * Print the current user's session token.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->auth->connect($this->config->getAccountId(), $this->config->getBearerToken());

        $token = $this->auth->getSessionToken();
        $tokenTtl = $this->auth->getSessionTokenExpiry();

        $response = [
            'temando_api_token' => $token,
            'temando_api_token_ttl' => $tokenTtl,
        ];

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($response);

        return $result;
    }
}
