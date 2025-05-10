<?php
/**
 * District5 Mondoc Builder Library
 *
 * @author      District5 <hello@district5.co.uk>
 * @copyright   District5 <hello@district5.co.uk>
 * @link        https://www.district5.co.uk
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
     * Find documents with a geospatial location within X miles of a given coordinate set.
     *
     * @param string $key
     * @param float|int $miles
     * @param float $longitude
     * @param float $latitude
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function withinXMilesOfCoordinates(string $key, float|int $miles, float $longitude, float $latitude): AbstractGeospatialPointNear
    {
        $distance = floatval($miles); // miles
        $metres = $distance * 1609.344; // meters
        $metres = intval(round($metres, 0, PHP_ROUND_HALF_UP));

        return $this->withinXMetresOfCoordinates(
            $key,
            $metres,
            $longitude,
            $latitude
        );
    }

    /**
     * Find documents with a geospatial location within X metres of a given coordinate set.
     *
     * @param string $key
     * @param int $metres
     * @param float $longitude
     * @param float $latitude
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
