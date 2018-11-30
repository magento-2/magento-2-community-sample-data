<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model\Plugin;

use Magento\Customer\Model\Customer\DataProvider;
use Vertex\Tax\Model\Repository\CustomerCodeRepository;

/**
 * Ensures the Vertex Customer Code is available in the Customer Admin Form
 *
 * @see DataProvider
 */
class CustomerDataProviderPlugin
{
    /** @var CustomerCodeRepository */
    private $repository;

    /**
     * @param CustomerCodeRepository $repository
     */
    public function __construct(CustomerCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Load the Vertex Customer Code into the Customer Data Provider for use in the Admin form
     *
     * @see DataProvider::getData() Intercepted method
     *
     * @param DataProvider $subject
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(DataProvider $subject, $data)
    {
        if (empty($data)) {
            return $data;
        }

        $customerIds = [];
        foreach ($data as $fieldData) {
            if (!isset($fieldData['customer']['entity_id'])) {
                continue;
            }

            $customerIds[] = $fieldData['customer']['entity_id'];
        }

        $customerCodes = $this->repository->getListByCustomerIds($customerIds);

        foreach ($data as $dataKey => $fieldData) {
            if (!isset($fieldData['customer']['entity_id'], $customerCodes[$fieldData['customer']['entity_id']])) {
                continue;
            }

            $entityId = $fieldData['customer']['entity_id'];
            $data[$dataKey]
                ['customer']
                ['extension_attributes']
                ['vertex_customer_code'] = $customerCodes[$entityId]->getCustomerCode();
        }

        return $data;
    }
}
