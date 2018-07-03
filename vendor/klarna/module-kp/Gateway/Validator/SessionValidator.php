<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Gateway\Validator;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SessionValidator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SessionValidator extends AbstractValidator
{
    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Constructor
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param StoreManagerInterface  $storeManager
     * @param ScopeConfigInterface   $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config
    ) {
        parent::__construct($resultFactory);
        $this->store = $storeManager->getStore();
        $this->config = $config;
    }

    /**
     * Validate
     *
     * @param bool  $isValid
     * @param array $fails
     * @return ResultInterface
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function validate(array $validationSubject)
    {
        $merchant_id = $this->config->getValue('klarna/api/merchant_id', ScopeInterface::SCOPE_STORES, $this->store);
        $secret = $this->config->getValue('klarna/api/shared_secret', ScopeInterface::SCOPE_STORES, $this->store);
        if (empty($merchant_id) || empty($secret)) {
            return $this->createResult(false, [__('Klarna API Credentials are required')]);
        }

        return $this->createResult(true);
    }
}
