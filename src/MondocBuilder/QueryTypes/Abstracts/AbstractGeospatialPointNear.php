<?php

/**
 * District5 - MondocBuilder
 *
 *  - A MongoDB query building library.
 *
 * @copyright District5
 *
 * @author District5
 * @link https://www.district5.co.uk
 *
 * @license This software and associated documentation (the "Software") may not be
 * used, copied, modified, distributed, published or licensed to any 3rd party
 * without the written permission of District5 or its author.
 *
 * The above copyright notice and this permission notice shall be included in
 * all licensed copies of the Software.
 */

namespace District5\MondocBuilder\QueryTypes\Abstracts;

/**
 * Class GeospatialPointNearSphere.
 *
 * @package District5\MondocBuilder\QueryTypes\Abstracts
 */
abstract class AbstractGeospatialPointNear extends AbstractQueryType
{
    /**
     * Find documents with a geospatial location within X metres of a given coordinate set.
     *
     * @param string $key
     * @param int    $metres
     * @param float  $longitude
     * @param float  $latitude
     *
     * @return $this
     */
    public function withinXMetresOfCoordinates(string $key, int $metres, float $longitude, float $latitude): AbstractGeospatialPointNear
    {
        $this->parts[$key] = [
            $metres,
            [
                $longitude,
                $latitude,
            ],
        ];

        return $this;
    }

    /**
     * Find documents with a geospatial location within X miles of a given coordinate set.
     *
     * @param string    $key
     * @param float|int $miles
     * @param float     $longitude
     * @param float     $latitude
     *
     * @return $this
     */
    public function withinXMilesOfCoordinates(string $key, $miles, float $longitude, float $latitude): AbstractGeospatialPointNear
    {
        $distance = floatval($miles); // miles
        $metres = $distance * 1609.344; // meters
        $metres = intval(round(floatval($metres), 0, PHP_ROUND_HALF_UP));

        return $this->withinXMetresOfCoordinates(
            $key,
            $metres,
            $longitude,
            $latitude
        );
    }

    /**
     * Get the array version of this query part.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $base = [];
        foreach ($this->parts as $key => $data) {
            $base[$key] = [
                $this->getOperator() => [
                    '$geometry' => [
                        'type' => 'Point',
                        'coordinates' => $data[1],
                    ],
                    '$maxDistance' => $data[0],
                ],
            ];
        }

        return $base;
    }

    /**
     * Get the operator for this object.
     *
     * @return string
     */
    abstract protected function getOperator(): string;
}
