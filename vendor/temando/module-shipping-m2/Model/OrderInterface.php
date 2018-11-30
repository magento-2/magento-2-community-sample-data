<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model;

/**
 * Temando Order Interface
 *
 * An order entity at the Temando platform.
 *
 * @package  Temando\Shipping\Model
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
interface OrderInterface
{
    const ORDER_ID = 'order_id';
    const CREATED_AT = 'created_at';
    const LAST_MODIFIED_AT = 'last_modified_at';
    const ORDERED_AT = 'ordered_at';

    const STATUS = 'status';
    const STATUS_AWAITING_PAYMENT = 'awaiting payment';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CLOSED = 'closed';

    const BILLING = 'billing';
    const RECIPIENT = 'recipient';
    const ORDER_ITEMS = 'order_items';

    const CURRENCY = 'currency';
    const AMOUNT = 'amount';

    const SOURCE_REFERENCE = 'source_reference';
    const SOURCE_ID = 'source_id';
    const SOURCE_INCREMENT_ID = 'source_increment_id';

    const CHECKOUT_FIELDS = 'checkout_fields';

    const COLLECTION_POINT = 'collection_point';
    const COLLECTION_POINT_SEARCH_REQUEST = 'collection_point_search_request';

    const SELECTED_EXPERIENCE_CODE = 'experience_code';
    const SELECTED_EXPERIENCE_CURRENCY = 'experience_currency';
    const SELECTED_EXPERIENCE_AMOUNT = 'experience_amount';
    const SELECTED_EXPERIENCE_LANGUAGE = 'experience_language';
    const SELECTED_EXPERIENCE_DESCRIPTION = 'experience_description';

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getLastModifiedAt();

    /**
     * @return string
     */
    public function getOrderedAt();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return \Temando\Shipping\Model\Order\OrderBillingInterface
     */
    public function getBilling();

    /**
     * @return \Temando\Shipping\Model\Order\OrderRecipientInterface
     */
    public function getRecipient();

    /**
     * @return \Temando\Shipping\Model\Order\OrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getSourceReference();

    /**
     * @return string
     */
    public function getSourceId();

    /**
     * @return string
     */
    public function getSourceIncrementId();

    /**
     * @return \Temando\Shipping\Model\Checkout\Attribute\CheckoutFieldInterface[]
     */
    public function getCheckoutFields();

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\QuoteCollectionPointInterface
     */
    public function getCollectionPoint();

    /**
     * @return \Temando\Shipping\Api\Data\CollectionPoint\SearchRequestInterface
     */
    public function getCollectionPointSearchRequest();

    /**
     * @return string
     */
    public function getExperienceCode();

    /**
     * @return string
     */
    public function getExperienceCurrency();

    /**
     * @return float
     */
    public function getExperienceAmount();

    /**
     * @return string
     */
    public function getExperienceLanguage();

    /**
     * @return string
     */
    public function getExperienceDescription();
}
