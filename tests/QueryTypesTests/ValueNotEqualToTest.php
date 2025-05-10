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

use District5\Date\Date;
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;
use District5Tests\MondocBuilderTests\TestQueryTypeAbstract;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;

/**
 * Class ValueNotEqualToTest
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class ValueNotEqualToTest extends TestQueryTypeAbstract
{
    public function testQueryType()
    {
        $nowPhpDateTime = Date::nowUtc();
        $nowUTCDateTime = Date::mongo()->convertTo($nowPhpDateTime);
        $newObjectId = new ObjectId();
        $query = ValueNotEqualTo::get()->string(
            'stringKey',
            'foo'
        )->integer(
            'intKey',
            100
        )->float(
            'floatKey',
            100.1
        )->double( // same as float
            'doubleKey',
            101.1
        )->boolean(
            'booleanKey',
            true
        )->null(
            'nullKey'
        )->mongoNative(
            'mongoKey',
            new Int64(100)
        )->objectId(
            'objectIdKey',
            $newObjectId
        )->dateTime(
            'dateTimeKey',
            $nowPhpDateTime
        )->utcDateTime(
            'utcDateTimeKey',
            $nowUTCDateTime
        );
        $this->assertEquals(
            [
                'stringKey' => [
                    '$ne' => 'foo',
                ],
                'intKey' => [
                    '$ne' => 100,
                ],
                'floatKey' => [
                    '$ne' => 100.1,
                ],
                'doubleKey' => [
                    '$ne' => 101.1,
                ],
                'booleanKey' => [
                    '$ne' => true,
                ],
                'nullKey' => [
                    '$ne' => null,
                ],
                'mongoKey' => [
                    '$ne' => new Int64(100),
                ],
                'objectIdKey' => [
                    '$ne' => $newObjectId,
                ],
                'dateTimeKey' => [
                    '$ne' => $nowUTCDateTime,
                ],
                'utcDateTimeKey' => [
                    '$ne' => $nowUTCDateTime,
                ],
            ],
            $query->getArrayCopy()
        );
    }

    public function testQueryTypeWithBuilder()
    {
        $builder = QueryBuilder::get();
        $nowPhpDateTime = Date::nowUtc();
        $nowUTCDateTime = Date::mongo()->convertTo($nowPhpDateTime);
        $newObjectId = new ObjectId();
        $query = ValueNotEqualTo::get()->string(
            'stringKey',
            'foo'
        )->integer(
            'intKey',
            100
        )->float(
            'floatKey',
            100.1
        )->double( // same as float
            'doubleKey',
            101.1
        )->boolean(
            'booleanKey',
            true
        )->null(
            'nullKey'
        )->mongoNative(
            'mongoKey',
            new Int64(100)
        )->objectId(
            'objectIdKey',
            $newObjectId
        )->dateTime(
            'dateTimeKey',
            $nowPhpDateTime
        )->utcDateTime(
            'utcDateTimeKey',
            $nowUTCDateTime
        );

        $builder->addQueryPart($query);
        $this->assertEquals(
            [
                'stringKey' => [
                    '$ne' => 'foo',
                ],
                'intKey' => [
                    '$ne' => 100,
                ],
                'floatKey' => [
                    '$ne' => 100.1,
                ],
                'doubleKey' => [
                    '$ne' => 101.1,
                ],
                'booleanKey' => [
                    '$ne' => true,
                ],
                'nullKey' => [
                    '$ne' => null,
                ],
                'mongoKey' => [
                    '$ne' => new Int64(100),
                ],
                'objectIdKey' => [
                    '$ne' => $newObjectId,
                ],
                'dateTimeKey' => [
                    '$ne' => $nowUTCDateTime,
                ],
                'utcDateTimeKey' => [
                    '$ne' => $nowUTCDateTime,
                ],
            ],
            $builder->getArrayCopy()
        );
    }
}
