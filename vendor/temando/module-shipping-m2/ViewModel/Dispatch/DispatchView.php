<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\Dispatch;

use Magento\Directory\Model\Currency;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Temando\Shipping\Model\DispatchProviderInterface;

/**
 * View model for dispatch view page.
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class DispatchView implements ArgumentInterface
{
    /**
     * @var DispatchProviderInterface
     */
    private $dispatchProvider;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * DispatchView constructor.
     * @param DispatchProviderInterface $dispatchProvider
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        DispatchProviderInterface $dispatchProvider,
        CurrencyFactory $currencyFactory
    ) {
        $this->dispatchProvider = $dispatchProvider;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @return DataObject[]
     */
    public function getPickupCharges(): array
    {
        $dispatch = $this->dispatchProvider->getDispatch();
        if (!$dispatch) {
            return [];
        }

        /** @var DataObject[] $pickupCharges */
        $pickupCharges = $dispatch->getPickupCharges();
        return $pickupCharges;
    }

    /**
     * @param string $currencyCode
     * @param float $amount
     * @return string
     */
    public function formatPrice(string $currencyCode, float $amount): string
    {
        if ($this->currency === null || $this->currency->getCurrencyCode() !== $currencyCode) {
            $this->currency = $this->currencyFactory->create(['data' => ['currency_code' => $currencyCode]]);
        }

        return $this->currency->format($amount);
    }
}
