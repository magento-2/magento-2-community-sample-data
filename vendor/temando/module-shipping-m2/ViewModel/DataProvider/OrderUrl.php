<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\UrlInterface;

/**
 * Order URL provider
 *
 * @package Temando\Shipping\ViewModel
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OrderUrl implements EntityUrlInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * OrderUrl constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getNewActionUrl(): string
    {
        return $this->urlBuilder->getUrl('sales/order_create/start');
    }

    /**
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->urlBuilder->getUrl('sales/order/index');
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getViewActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('sales/order/view', [
            'order_id' => $data['order_id'],
        ]);
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getEditActionUrl(array $data): string
    {
        return '';
    }

    /**
     * @param mixed[] $data Item data for the implementer to pick entity identifier.
     * @return string
     */
    public function getDeleteActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('sales/order/cancel', [
            'order_id' => $data['order_id'],
        ]);
    }
}
