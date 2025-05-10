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

namespace District5Tests\MondocBuilderTests\QueryTypesTests;

use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\GeospatialPointNear;
use District5\MondocBuilder\QueryTypes\GeospatialPointNearSphere;
use District5Tests\MondocBuilderTests\TestQueryTypeAbstract;

/**
 * Class GeospatialPointNearTest
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class GeospatialPointNearTest extends TestQueryTypeAbstract
{
    public function testQueryType()
    {
        $queryMetres = GeospatialPointNear::get()->withinXMetresOfCoordinates(
            'location_metres',
            40,
            -0.1415681,
            51.5006761
        );
        $this->assertEquals(
            [
                'location_metres' => [
                    '$near' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                -0.1415681,
                                51.5006761,
                            ],
                        ],
                        '$maxDistance' => 40,
                    ],
                ],
            ],
            $queryMetres->getArrayCopy()
        );

        $queryMiles = GeospatialPointNear::get()->withinXMilesOfCoordinates(
            'location_miles',
            1,
            -0.1415681,
            51.5006761
        );
        $this->assertEquals(
            [
                'location_miles' => [
                    '$near' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                -0.1415681,
                                51.5006761,
                            ],
                        ],
                        '$maxDistance' => 1609, // rounded down from 1609.344,
                    ],
                ],
            ],
            $queryMiles->getArrayCopy()
        );
    }

    public function testQueryTypeWithBuilder()
    {
        $builder = QueryBuilder::get();

        $queryMetres = GeospatialPointNearSphere::get()->withinXMetresOfCoordinates(
            'location_metres',
            40,
            -0.1415681,
            51.5006761
        );
        $builder->addQueryPart($queryMetres);

        $queryMiles = GeospatialPointNearSphere::get()->withinXMilesOfCoordinates(
            'location_miles',
            1,
            -0.1415681,
            51.5006761
        );
        $builder->addQueryPart($queryMiles);

        $this->assertEquals(
            [
                'location_metres' => [
                    '$nearSphere' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                -0.1415681,
                                51.5006761,
                            ],
                        ],
                        '$maxDistance' => 40,
                    ],
                ],
                'location_miles' => [
                    '$nearSphere' => [
                        '$geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                -0.1415681,
                                51.5006761,
                            ],
                        ],
                        '$maxDistance' => 1609, // rounded down from 1609.344,
                    ],
                ],
            ],
            $builder->getArrayCopy()
        );
    }
}
