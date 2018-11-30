<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */

namespace Temando\Shipping\Model\Delivery;

use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Temando\Shipping\Model\Config\ConfigAccessor;

/**
 * Temando Delivery Location Opening Hours Formatter
 *
 * @package Temando\Shipping\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
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
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var ConfigAccessor
     */
    private $scopeConfig;

    /**
     * OpeningHoursFormatter constructor.
     *
     * @param ScopeResolverInterface $scopeResolver
     * @param TimezoneInterface      $date
     * @param ResolverInterface      $localeResolver
     * @param ConfigAccessor         $scopeConfig
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        TimezoneInterface $date,
        ResolverInterface $localeResolver,
        ConfigAccessor $scopeConfig
    ) {
        $this->scopeResolver  = $scopeResolver;
        $this->date           = $date;
        $this->localeResolver = $localeResolver;
        $this->scopeConfig    = $scopeConfig;
    }

    /**
     * Format general opening hours, e.g.:
     * - Monday, Tuesday | 9:00 AM - 8:00 PM
     * - Montag, Dienstag | 09:00 - 20:00
     *
     * @param string[] $openingHours
     * @param string $locale
     * @return string[]
     */
    private function formatGeneralOpenings(array $openingHours, string $locale): array
    {
        // summarize days with the same opening hours
        $hoursMap = [];
        foreach ($openingHours as $day => $hours) {
            $key = crc32($hours['from'] . '#' . $hours['to']);
            if (!isset($hoursMap[$key])) {
                $hoursMap[$key] = [
                    'days' => [],
                    'from' => $hours['from'],
                    'to'   => $hours['to'],
                ];
            }

            $day = $this->date->date($day, $locale, false, false)->format('l');
            $hoursMap[$key]['days'][] = $day;
        }

        // localize times
        $generalOpenings = [];
        foreach ($hoursMap as $key => $details) {
            $days = implode(', ', $details['days']);

            $dateOpens = $this->date->date($details['from'], $locale, false, true);
            $dateCloses = $this->date->date($details['to'], $locale, false, true);

            $timeOpens = $this->date->formatDateTime(
                $dateOpens,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $locale,
                'UTC'
            );
            $timeCloses = $this->date->formatDateTime(
                $dateCloses,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $locale,
                'UTC'
            );

            $generalOpenings[] = [
                'days' => $days,
                'times' => sprintf('%s - %s', $timeOpens, $timeCloses),
            ];
        }

        return $generalOpenings;
    }

    /**
     * Format specifics, e.g.:
     * - Dec 24 9:00 AM - Jan 1 8:00 PM
     * - Jul 4 9:00 AM - 8:00 PM
     * - 24.12. 09:00 - 01.01. 20:00
     * - 03.10. 09:00 - 20:00
     *
     * @param string[] $openingHours
     * @param string $locale
     * @param int $offset
     * @return string[]
     */
    private function formatSpecifics(array $openingHours, string $locale, $offset = 7): array
    {
        $formattedSpecifics = [];
        $today = $this->date->date();

        foreach ($openingHours as $opening) {
            $dateFrom = $this->date->date($opening['from'], $locale, false, true);
            $dateTo = $this->date->date($opening['to'], $locale, false, true);

            $diff = $today->diff($dateFrom);
            if (!$diff->invert && ($diff->days > $offset)) {
                // do not display any specifics beginning more than $offset days in the future
                continue;
            }

            $diff = $today->diff($dateTo);
            if ($diff->invert) {
                // do not display any specifics ending in the past
                continue;
            }

            // extract year, will be stripped off later
            $dateFromYear = $dateFrom->format('Y');
            $dateToYear = $dateTo->format('Y');

            // start date, date part
            $dateFromDate = $this->date->formatDateTime(
                $dateFrom,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE,
                $locale,
                'UTC'
            );
            // end date, date part
            $dateToDate = $this->date->formatDateTime(
                $dateTo,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE,
                $locale,
                'UTC'
            );

            // start date, time part
            $dateFromTime = $timeOpens = $this->date->formatDateTime(
                $dateFrom,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $locale,
                'UTC'
            );
            // end date, time part
            $dateToTime = $timeOpens = $this->date->formatDateTime(
                $dateTo,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $locale,
                'UTC'
            );

            $dateFromDate = trim(str_replace($dateFromYear, '', $dateFromDate), ' ,');
            $dateToDate = trim(str_replace($dateToYear, '', $dateToDate), ' ,');

            $formattedDateFrom = sprintf('%s %s', $dateFromDate, $dateFromTime);
            $formattedDateTo = sprintf('%s %s', $dateToDate, $dateToTime);
            if ($dateFromDate == $dateToDate) {
                $formattedDateTo = $dateToTime;
            }

            $formattedSpecifics[] = [
                'description' => __($opening['description']),
                'from' => $formattedDateFrom,
                'to' => $formattedDateTo,
            ];
        }

        return $formattedSpecifics;
    }

    /**
     * Combine and format opening hours.
     *
     * @param string[] $openingHours
     *
     * @return string[]
     */
    public function format(array $openingHours): array
    {
        $locale = $this->localeResolver->getLocale();

        $formattedOpenings = [];
        if (isset($openingHours['general'])) {
            $formattedOpenings = $this->formatGeneralOpenings($openingHours['general'], $locale);
        }

        $formattedSpecifics = [
            'openings' => [],
            'closures' => [],
        ];
        if (isset($openingHours['specific']) && isset($openingHours['specific']['openings'])) {
            $formattedSpecifics['openings'] = $this->formatSpecifics($openingHours['specific']['openings'], $locale);
        }
        if (isset($openingHours['specific']) && isset($openingHours['specific']['closures'])) {
            $formattedSpecifics['closures'] = $this->formatSpecifics($openingHours['specific']['closures'], $locale);
        }

        $formatted = [
            'general'  => $formattedOpenings,
            'specific' => $formattedSpecifics,
        ];

        return $formatted;
    }
}
