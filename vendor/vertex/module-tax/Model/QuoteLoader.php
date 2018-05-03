<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as ResourceModel;

/**
 * Loads Quote Models
 */
class QuoteLoader
{
    /** @var QuoteFactory */
    private $modelFactory;

    /** @var ResourceModel */
    private $resourceModel;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var GuestCartRepositoryInterface */
    private $guestCartRepository;

    /**
     * @param QuoteFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param CartRepositoryInterface $cartRepository
     * @param GuestCartRepositoryInterface $guestCartRepository
     */
    public function __construct(
        QuoteFactory $modelFactory,
        ResourceModel $resourceModel,
        CartRepositoryInterface $cartRepository,
        GuestCartRepositoryInterface $guestCartRepository
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->cartRepository = $cartRepository;
        $this->guestCartRepository = $guestCartRepository;
    }

    /**
     * Retrieve a Quote Model given a Quote/Cart ID
     *
     * @param int $quoteId
     * @return Quote|null
     */
    public function getQuoteModelById($quoteId)
    {
        // Attempt loading via repository first - we want to use the API whenever possible
        try {
            $quote = $this->cartRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        if ($quote instanceof Quote) {
            return $quote;
        }

        // If we've gotten this far then the quote exists but it's not a quote model - right now we need one.

        /** @var Quote $quote */
        $quote = $this->modelFactory->create();
        $this->resourceModel->load($quote, $quoteId);

        return $quote->getId() ? $quote : null;
    }

    /**
     * Retrieve a Quote Model given a masked Quote/Cart ID
     *
     * @param string|int $quoteId
     * @return Quote|null
     */
    public function getGuestQuoteModelById($quoteId)
    {
        // Attempt loading via repository first - we want to use the API whenever possible
        try {
            $quote = $this->guestCartRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        if ($quote instanceof Quote) {
            return $quote;
        }

        // If we've gotten this far then the quote exists but it's not a quote model - right now we need one.

        $quoteId = $quote->getId();

        /** @var Quote $quote */
        $quote = $this->modelFactory->create();
        $this->resourceModel->load($quote, $quoteId);

        return $quote->getId() ? $quote : null;
    }
}
