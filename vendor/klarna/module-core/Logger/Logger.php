<?php
/**
 * This file is part of the Klarna Core module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Core\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Monolog\Logger as MonoLogger;

/**
 * Class Logger
 *
 * @package Klarna\Core\Logger
 */
class Logger extends MonoLogger
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Cleanser
     */
    private $cleanser;

    /**
     * Logger constructor.
     *
     * @param string                              $name
     * @param ScopeConfigInterface                $config
     * @param Cleanser                            $cleanser
     * @param StoreManagerInterface               $storeManager
     * @param \Monolog\Handler\HandlerInterface[] $handlers
     * @param \callable[]                         $processors
     * @codeCoverageIgnore
     */
    public function __construct(
        $name,
        ScopeConfigInterface $config,
        Cleanser $cleanser,
        StoreManagerInterface $storeManager,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);
        $this->config = $config;
        $this->cleanser = $cleanser;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addRecord($level, $message, array $context = [])
    {
        $store = $this->storeManager->getStore();
        if (!$this->config->isSetFlag(
            'klarna/api/debug',
            ScopeInterface::SCOPE_STORE,
            $store
        )
        ) {
            return false;
        }
        if (is_string($message) || null === $message) {
            return parent::addRecord($level, $message, $context);
        }
        if (!$this->config->isSetFlag('klarna/api/test_mode', ScopeInterface::SCOPE_STORE, $store)) {
            // We only need to "clean" the log data if Live
            $message = $this->cleanser->checkForSensitiveData($message);
        }
        if (is_array($message)) {
            $message = print_r($message, true);
        }
        return parent::addRecord($level, $message, $context);
    }
}
