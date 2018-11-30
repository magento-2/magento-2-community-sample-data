<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Temando\Shipping\Model\Config\Backend\Active\CredentialsValidator;

/**
 * Validate Temando API credentials before enabling the carrier.
 *
 * @package  Temando\Shipping\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class Active extends Value
{
    /**
     * @var CredentialsValidator
     */
    private $validationRules;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param CredentialsValidator $validationRules
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        CredentialsValidator $validationRules,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->validationRules = $validationRules;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Template method to return validate rules for the entity
     *
     * @return \Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $inputValidator = $this->validationRules->getInputValidator();
        $authenticationValidator = $this->validationRules->getAuthenticationValidator();

        $validatorChain = new \Zend_Validate();
        $validatorChain->addValidator($inputValidator, true);
        $validatorChain->addValidator($authenticationValidator, true);

        return $validatorChain;
    }
}
