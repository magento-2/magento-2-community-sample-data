<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\ViewModel\DataProvider;

use Magento\Framework\UrlInterface;
use Temando\Shipping\Model\BatchInterface;

/**
 * Batch URL provider
 *
 * @package  Temando\Shipping\ViewModel
 * @author   Rhodri Davies <rhodri.davies@temando.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class BatchUrl implements EntityUrlInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * BatchUrl constructor.
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
        return $this->urlBuilder->getUrl('temando/batch/create');
    }

    /**
     * @return string
     */
    public function getListActionUrl(): string
    {
        return $this->urlBuilder->getUrl('temando/batch/index');
    }

    /**
     * @param mixed[] $data Item data to pick entity identifier.
     * @return string
     */
    public function getViewActionUrl(array $data): string
    {
        return $this->urlBuilder->getUrl('temando/batch/view', [
            BatchInterface::BATCH_ID => $data[BatchInterface::BATCH_ID],
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
        return $this->urlBuilder->getUrl('temando/batch/solve', [
            BatchInterface::BATCH_ID => $data[BatchInterface::BATCH_ID],
        ]);
    }

    /**
     * @param array  $data
     * @param string $batchId
     *
     * @return string
     */
    public function getPrintAllPackingSlips(array $data, string $batchId): string
    {
        $ids = implode(",", $data);

        return $this->urlBuilder->getUrl(
            'temando/batch/printpackageslips',
            ['order_ids' => $ids, 'batch_id' => $batchId]
        );
    }
}
