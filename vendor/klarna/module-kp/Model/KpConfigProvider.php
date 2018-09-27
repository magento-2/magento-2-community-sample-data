<?php
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model;

use Klarna\Core\Exception as KlarnaException;
use Klarna\Core\Helper\ConfigHelper;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class KpConfigProvider
 *
 * @package Klarna\Kp\Model
 */
class KpConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigHelper
     */
    private $config;

    /**
     * @var Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param ConfigHelper $config
     * @param Session      $session
     */
    public function __construct(ConfigHelper $config, Session $session)
    {
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Return payment config for frontend JS to use
     *
     * @return string[][]
     * @throws \Klarna\Core\Model\Api\Exception
     */
    public function getConfig()
    {
        $store = $this->session->getQuote()->getStore();
        $paymentConfig = [
            'payment' => [
                'klarna_kp' => [
                    'client_token'      => null,
                    'message'           => null,
                    'success'           => 0,
                    'debug'             => $this->config->isApiConfigFlag('debug', $store),
                    'enabled'           => $this->config->isPaymentConfigFlag('active', $store, Kp::METHOD_CODE),
                    'available_methods' => [
                        'type'      => 'klarna_kp',
                        'component' => 'Klarna_Kp/js/view/payments/kp'
                    ]
                ]
            ]
        ];

        if (!$this->config->isPaymentConfigFlag('active', $store, Kp::METHOD_CODE)) {
            $paymentConfig['payment']['klarna_kp']['message'] = __('Klarna Payments is not enabled');
            return $paymentConfig;
        }
        try {
            /** @var ResponseInterface $response */
            $response = $this->session->getApiResponse();
            if ($response === null) {
                $response = $this->session->init();
            }
            $klarnaQuote = $this->session->getKlarnaQuote();
            if ($klarnaQuote->getClientToken() === '') {
                $paymentConfig['payment']['klarna_kp']['message'] = __('Please check credentials');
                return $paymentConfig;
            }

            $methods = $klarnaQuote->getPaymentMethodInfo();
            $paymentConfig['payment']['klarna_kp']['client_token'] = $klarnaQuote->getClientToken();
            $paymentConfig['payment']['klarna_kp']['authorization_token'] = $klarnaQuote->getAuthorizationToken();
            $paymentConfig['payment']['klarna_kp']['success'] = $response->isSuccessfull() ? 1 : 0;
            if (!$response->isSuccessfull()) {
                $paymentConfig['payment']['klarna_kp']['message'] = $response->getMessage();
                return $paymentConfig;
            }
            $paymentConfig = $this->getAvailableMethods($paymentConfig, $methods);
        } catch (KlarnaException $e) {
            $paymentConfig['payment']['klarna_kp']['message'] = $e->getMessage();
        }
        return $paymentConfig;
    }

    /**
     * Get available payment methods
     *
     * @param array $config
     * @param array $methods
     * @return array
     */
    private function getAvailableMethods($config, $methods)
    {
        $available_methods = [];
        foreach ($methods as $method) {
            $identifier = $method->identifier;
            $available_methods[] = [
                'type'      => 'klarna_' . $identifier,
                'component' => 'Klarna_Kp/js/view/payments/kp'
            ];
            $config['payment'][$identifier] = $config['payment']['klarna_kp'];
            $config['payment']['klarna_kp'][$identifier] = [
                'title' => $method->name,
                'logo'  => $method->asset_urls->standard
            ];
            unset($config['payment'][$identifier]['available_methods']);
        }
        $config['payment']['klarna_kp']['available_methods'] = $available_methods;
        return $config;
    }
}
