<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Controller\Adminhtml\Activation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Temando\Shipping\Model\Config\ModuleConfigInterface;

/**
 * Temando Activation Notice
 *
 * @package  Temando\Shipping\Controller
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
abstract class AbstractRegisteredAction extends Action
{
    /**
     * @var ModuleConfigInterface
     */
    private $config;

    /**
     * @param Context $context
     * @param ModuleConfigInterface $config
     */
    public function __construct(Context $context, ModuleConfigInterface $config)
    {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        if (!$this->config->isRegistered()) {
            $subject = $request->getControllerName();

            // only pass on the last part of the controller name.
            $delimiterPos = strpos($subject, '_');
            if ($delimiterPos !== false) {
                $subject = substr($subject, 1 + strpos($subject, '_'));
            }

            $this->_forward('notice', 'activation', $request->getModuleName(), [
                'subject' => $subject
            ]);

            return $this->getResponse();
        }

        return parent::dispatch($request);
    }
}
