<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\Quote;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Temando\Shipping\Model\CollectionPoint\CollectionPointManagement;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

/**
 * Manage delivery options.
 *
 * @package Temando\Shipping\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class DeliveryOptionManagement
{
    const DELIVERY_OPTION_TO_ADDRESS = 'toAddress';
    const DELIVERY_OPTION_TO_COLLECTION_POINT = 'toCollectionPoint';

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CollectionPointManagement
     */
    private $collectionPointManagement;

    /**
     * DeliveryOptionManagement constructor.
     *
     * @param AddressRepositoryInterface $addressRepository
     * @param CollectionPointManagement $collectionPointManagement
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CollectionPointManagement $collectionPointManagement
    ) {
        $this->addressRepository = $addressRepository;
        $this->collectionPointManagement = $collectionPointManagement;
    }

    /**
     * Clean up previously selected option.
     *
     * @param int $shippingAddressId
     * @param string $selectedOption
     * @return void
     * @throws CouldNotDeleteException
     */
    private function cleanUpPreviousOption($shippingAddressId, $selectedOption)
    {
        switch ($selectedOption) {
            case self::DELIVERY_OPTION_TO_ADDRESS:
                $this->collectionPointManagement->deleteSearchRequest($shippingAddressId);
                break;
            case self::DELIVERY_OPTION_TO_COLLECTION_POINT:
                $this->addressRepository->deleteByShippingAddressId($shippingAddressId);
                break;
        }
    }

    /**
     * Indicate a collection point search as pending. That is, the delivery
     * option was chosen but no collection point search was triggered yet.
     *
     * @param int $shippingAddressId
     * @param string $selectedOption
     * @return void
     * @throws CouldNotDeleteException
     */
    private function setOptionPending($shippingAddressId, $selectedOption)
    {
        if ($selectedOption !== self::DELIVERY_OPTION_TO_COLLECTION_POINT) {
            return;
        }

        $this->collectionPointManagement->saveSearchRequest($shippingAddressId, '', '', true);
    }

    /**
     * Perform actions when the selected delivery option changed.
     *
     * @param int $shippingAddressId
     * @param string $selectedOption
     * @throws LocalizedException
     */
    public function selectOption($shippingAddressId, $selectedOption)
    {
        try {
            $this->cleanUpPreviousOption($shippingAddressId, $selectedOption);
            $this->setOptionPending($shippingAddressId, $selectedOption);
        } catch (CouldNotDeleteException $exception) {
            throw new LocalizedException(__('Delivery option processing failed.'));
        }
    }
}
