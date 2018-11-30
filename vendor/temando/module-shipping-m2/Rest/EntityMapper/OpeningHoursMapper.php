<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\EntityMapper;

use Temando\Shipping\Rest\Response\Fields\Location\OpeningHours;

/**
 * Map API data to application data object
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
class OpeningHoursMapper
{
    /**
     * Sort opening hours by day of week.
     *
     * Date formats:
     * [
     *      "general => ["l" => ["from" => "H:i:sP", "to" => "H:i:sP"]],
     *      "specific" => [
     *          "openings" => [
     *              ["description" => "some special opening", "from" => "c", "to" => "c"],
     *              ["description" => "another special opening", "from" => "c", "to" => "c"],
     *          ],
     *          "closures" => [
     *              ["description" => "some holiday", "from" => "c", "to" => "c"],
     *              ["description" => "another special closure", "from" => "c", "to" => "c"],
     *          ],
     *      ]
     * }
     *
     * @param OpeningHours $apiHours
     * @return string[][]
     */
    public function map(OpeningHours $apiHours)
    {
        // general opening hours
        $openingHours = [];
        // specific opening hours
        $openingHoursSpecification = [
            'openings' => [],
            'closures' => [],
        ];

        foreach ($apiHours->getDefault() as $item) {
            $dow = $item->getDayOfWeek();
            $openingHours[$dow] = [
                'from' => $item->getOpens(),
                'to' => $item->getCloses(),
            ];
        }

        if ($apiHours->getExceptions()) {
            foreach ($apiHours->getExceptions()->getOpenings() as $opening) {
                $openingHoursSpecification['openings'][] = [
                    'description' => $opening->getDescription(),
                    'from' => $opening->getFrom(),
                    'to' => $opening->getTo(),
                ];
            }

            foreach ($apiHours->getExceptions()->getClosures() as $closure) {
                $openingHoursSpecification['closures'][] = [
                    'description' => $closure->getDescription(),
                    'from' => $closure->getFrom(),
                    'to' => $closure->getTo(),
                ];
            }
        }

        $openingHours = [
            'general' => $openingHours,
            'specific' => $openingHoursSpecification,
        ];

        return $openingHours;
    }
}
