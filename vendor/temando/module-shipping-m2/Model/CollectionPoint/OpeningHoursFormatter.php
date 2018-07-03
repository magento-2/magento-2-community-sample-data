<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Model\CollectionPoint;

use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Temando Collection Point Opening Hours Formatter
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    http://www.temando.com/
 */
class OpeningHoursFormatter
{
    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * OpeningHoursFormatter constructor.
     * @param ScopeResolverInterface $scopeResolver
     * @param TimezoneInterface $date
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        TimezoneInterface $date
    ) {
        $this->scopeResolver = $scopeResolver;
        $this->date = $date;
    }

    /**
     * Combine and format opening times.
     *
     * @param string[] $openingHours
     * @return string[]
     */
    public function format(array $openingHours)
    {
        $timezone = $this->date->getConfigTimezone(
            $this->scopeResolver->getScope()->getScopeType(),
            $this->scopeResolver->getScope()->getId()
        );

        $hoursMap = [];
        foreach ($openingHours as $day => $hours) {
            $key = crc32($hours['from'] . '#' .  $hours['to']);
            if (!isset($hoursMap[$key])) {
                $hoursMap[$key] = [
                    'days' => [],
                    'from' => $hours['from'],
                    'to' => $hours['to'],
                ];
            }

            $day = $this->date->date($day, null, true, false)->format('D');
            $hoursMap[$key]['days'][]= $day;
        }

        $formatted = [];
        foreach ($hoursMap as $key => $details) {
            $days = implode(', ', $details['days']);

            $dateOpens = $this->date->date($details['from']);
            $dateCloses = $this->date->date($details['to']);

            $dateOpens->setTimezone(new \DateTimeZone($timezone));
            $dateCloses->setTimezone(new \DateTimeZone($timezone));

            $formatted[] = [
                'days' => $days,
                'times' => sprintf('%s - %s', $dateOpens->format('g a'), $dateCloses->format('g a')),
            ];
        }

        return $formatted;
    }
}
