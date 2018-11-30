<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Temando\Shipping\Api\Data\Order\OrderReferenceInterfaceFactory;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterface;
use Temando\Shipping\Api\Data\Order\ShippingExperienceInterfaceFactory;
use Temando\Shipping\Rest\Response\Type\Generic\Experience;
use Temando\Shipping\Rest\Response\Type\Generic\Experience\Cost;
use Temando\Shipping\Rest\Response\Type\Generic\Experience\Description;

/**
 * Map API data to application data object
 *
 * @package  Temando\Shipping\Rest
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class ShippingExperiencesMapper
{
    /**
     *  ResolverInterface
     */
    private $localeResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ShippingExperienceInterfaceFactory
     */
    private $shippingExperienceFactory;

    /**
     * @var OrderReferenceInterfaceFactory
     */
    private $orderReferenceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ShippingExperiencesMapper constructor.
     * @param OrderReferenceInterfaceFactory $orderReferenceFactory
     * @param ResolverInterface $localeResolver
     * @param StoreManagerInterface $storeManager
     * @param ShippingExperienceInterfaceFactory $shippingExperienceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderReferenceInterfaceFactory $orderReferenceFactory,
        ResolverInterface $localeResolver,
        StoreManagerInterface $storeManager,
        ShippingExperienceInterfaceFactory $shippingExperienceFactory,
        LoggerInterface $logger
    ) {
        $this->orderReferenceFactory = $orderReferenceFactory;
        $this->localeResolver = $localeResolver;
        $this->logger = $logger;
        $this->shippingExperienceFactory = $shippingExperienceFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Experience[] $apiExperiences
     *
     * @return ShippingExperienceInterface[]
     */
    public function map(array $apiExperiences)
    {
        $shippingExperiences = [];

        foreach ($apiExperiences as $apiExperience) {
            try {
                $cost = $this->extractShippingCost($apiExperience->getCost());
            } catch (LocalizedException $e) {
                continue;
            }

            $description = $this->getLocalizedDescription($apiExperience->getDescription());
            $shippingExperience = $this->shippingExperienceFactory->create([
                ShippingExperienceInterface::LABEL => $description,
                ShippingExperienceInterface::CODE => $apiExperience->getCode(),
                ShippingExperienceInterface::COST => $cost,
            ]);

            $shippingExperiences[]= $shippingExperience;
        }

        if (empty($shippingExperiences)) {
            $this->logger->error(__('No applicable shipping cost found in webservice response.'));
        }

        return $shippingExperiences;
    }

    /**
     * @param Cost[] $cost
     *
     * @return float
     * @throws LocalizedException
     */
    private function extractShippingCost(array $cost)
    {
        /** @var \Magento\Store\Model\Store $currentStore */
        $currentStore = $this->storeManager->getStore();
        $baseCurrency = $currentStore->getBaseCurrencyCode();

        $warningTemplate = "%1 is not a valid shipping method currency. Use %2 when configuring rates.";

        $applicableCosts = array_filter($cost, function (Cost $item) use ($baseCurrency, $warningTemplate) {
            if ($item->getCurrency() !== $baseCurrency) {
                $message = __($warningTemplate, $item->getCurrency(), $baseCurrency);
                $this->logger->warning($message->render());

                return false;
            }

            return true;
        });

        if (empty($applicableCosts)) {
            throw new NotFoundException(__('No applicable shipping cost found.'));
        }

        // return first available cost
        $item = current($applicableCosts);
        return $item->getAmount();
    }

    /**
     * @param Description[] $descriptions
     * @return string
     */
    private function getLocalizedDescription(array $descriptions)
    {
        $descriptionFilter = function ($descriptions, $locale) {
            /** @var Description $description */
            foreach ($descriptions as $description) {
                if ($description->getLocale() === $locale) {
                    return $description;
                }
            }

            return null;
        };

        // try locale exact match first
        $locale = $this->localeResolver->getLocale();
        $fallbacks = [$locale, substr($locale, 0, 2), 'en'];

        do {
            $lang = array_shift($fallbacks);
            $localizedDescription = $descriptionFilter($descriptions, $lang);
        } while (!empty($fallbacks) && !$localizedDescription);

        return ($localizedDescription ? $localizedDescription->getText() : '');
    }
}
