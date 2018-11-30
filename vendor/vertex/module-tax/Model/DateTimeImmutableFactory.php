<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Tax\Model;

/**
 * Factory for {@see \DateTimeImmutable}
 */
class DateTimeImmutableFactory
{
    /**
     * Create an instance of {@see \DateTimeImmutable}
     *
     * @param string $time
     * @param \DateTimeZone|null $timezone
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    public function create($time = 'now', \DateTimeZone $timezone = null)
    {
        return new \DateTimeImmutable($time, $timezone);
    }
}
