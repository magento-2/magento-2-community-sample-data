<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\UrlInterface;
use Temando\Shipping\Model\DispatchInterface;

/**
 * Dispatch URL provider
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class DispatchUrl implements EntityUrlInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CarrierUrl constructor.
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
        return $this->urlBuilder->getUrl('temando/dispatch/new');
    }

    /**
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/dispatch/index');
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getViewActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('temando/dispatch/view', [
            DispatchInterface::DISPATCH_ID => $data[DispatchInterface::DISPATCH_ID],
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
        return '';
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getSolveActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('temando/dispatch/solve', [
            DispatchInterface::DISPATCH_ID => $data[DispatchInterface::DISPATCH_ID],
        ]);
    }
}
