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

namespace District5Tests\MondocBuilderTests\BulkTests;

use District5\Date\Date;
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\KeyExists;
use District5\MondocBuilder\QueryTypes\NorOperator;
use District5\MondocBuilder\QueryTypes\OrOperator;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotInValues;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class LargeOrTest
 *
 * @package District5\MondocBuilderTests\BulkTests
 *
 * @internal
 */
class LargeOrTest extends TestCase
{
    public function testBigOr()
    {
        $dateFrom = Date::modify(Date::nowUtc())->minus()->days(30);
        $dateTo = Date::nowUtc();

        $idOne = new ObjectId('6612d694ee06f31b062f7fd0');
        $idTwo = new ObjectId('5e6257472afe4b41e941942e');

        $queryBuilder = QueryBuilder::get();
        $or = OrOperator::get();

        $firstOr = QueryBuilder::get();
        $documentOneOne = ValueNotEqualTo::get()->null('document.refId');
        $documentTwoOne = ValueNotEqualTo::get()->null('document.appId');
        $spamOne = ValueEqualTo::get()->boolean('spam', false);
        $createdGreaterThanOne = ValueGreaterThanOrEqualTo::get()->dateTime('cd', $dateFrom);
        $createdLessThanOne = ValueLessThanOrEqualTo::get()->dateTime('cd', $dateTo);
        $notInOne = ValueNotInValues::get()->anArray('class', [$idOne, $idTwo]);
        $sourceOne = ValueEqualTo::get()->string('source', 'user_submitted');
        $encryptedOne = KeyExists::get()->false('encrypted');
        $firstOr->addQueryPart(
            $documentOneOne
        )->addQueryPart(
            $documentTwoOne
        )->addQueryPart(
            $spamOne
        )->addQueryPart(
            $createdLessThanOne
        )->addQueryPart(
            $createdGreaterThanOne
        )->addQueryPart(
            $notInOne
        )->addQueryPart(
            $sourceOne
        )->addQueryPart(
            $encryptedOne
        );

        $or->addBuilder($firstOr);

        $secondOr = QueryBuilder::get();
        $documentOneTwo = ValueNotEqualTo::get()->null('document.refId');
        $documentTwoTwo = ValueNotEqualTo::get()->null('document.appId');
        $spamTwo = ValueEqualTo::get()->boolean('spam', false);
        $createdGreaterThanTwo = ValueGreaterThanOrEqualTo::get()->dateTime('cd', $dateFrom);
        $createdLessThanTwo = ValueLessThanOrEqualTo::get()->dateTime('cd', $dateTo);
        $notInTwo = ValueNotInValues::get()->anArray('class', [$idOne, $idTwo]);
        $sourceTwo = ValueEqualTo::get()->string('source', 'user_submitted');
        $encryptedTwo = ValueEqualTo::get()->boolean('encrypted', true);
        $secondOr->addQueryPart(
            $documentOneTwo
        )->addQueryPart(
            $documentTwoTwo
        )->addQueryPart(
            $spamTwo
        )->addQueryPart(
            $createdLessThanTwo
        )->addQueryPart(
            $createdGreaterThanTwo
        )->addQueryPart(
            $notInTwo
        )->addQueryPart(
            $sourceTwo
        )->addQueryPart(
            $encryptedTwo
        );

        $or->addBuilder($secondOr);

        $thirdOr = QueryBuilder::get();
        $documentOneThree = ValueNotEqualTo::get()->null('document.refId');
        $documentTwoThree = ValueNotEqualTo::get()->null('document.appId');
        $spamThree = ValueEqualTo::get()->boolean('spam', false);
        $createdLessThanThree = ValueLessThanOrEqualTo::get()->dateTime('cd', $dateTo);
        $createdGreaterThanThree = ValueGreaterThanOrEqualTo::get()->dateTime('cd', $dateFrom);
        $notInThree = ValueNotInValues::get()->anArray('class', [$idOne, $idTwo]);
        $sourceThree = ValueEqualTo::get()->string('source', 'user_submitted');
        $encryptedThree = ValueEqualTo::get()->boolean('encrypted', false);
        $scanExistsThree = ValueEqualTo::get()->string('scanState', 'scanned');
        $thirdOr->addQueryPart(
            $documentOneThree
        )->addQueryPart(
            $documentTwoThree
        )->addQueryPart(
            $spamThree
        )->addQueryPart(
            $createdLessThanThree
        )->addQueryPart(
            $createdGreaterThanThree
        )->addQueryPart(
            $notInThree
        )->addQueryPart(
            $sourceThree
        )->addQueryPart(
            $encryptedThree
        )->addQueryPart(
            $scanExistsThree
        );

        $or->addBuilder($thirdOr);

        $queryBuilder->addQueryPart($or);

        $nor = NorOperator::get();

        $firstNor = QueryBuilder::get();
        $norPartOne = ValueNotEqualTo::get()->float('price', 0.0);
        $norPartTwo = KeyExists::get()->true('price');
        $norPartThree = ValueEqualTo::get()->boolean('price', false);
        $firstNor->addQueryPart(
            $norPartOne
        )->addQueryPart(
            $norPartTwo
        )->addQueryPart(
            $norPartThree
        );
        $nor->addBuilder($firstNor);

        $secondNor = QueryBuilder::get();
        $norPartFour = ValueEqualTo::get()->boolean('cancelled', true);
        $norPartFive = ValueEqualTo::get()->boolean('refunded', true);
        $norPartSix = ValueEqualTo::get()->boolean('deleted', true);
        $secondNor->addQueryPart(
            $norPartFour
        )->addQueryPart(
            $norPartFive
        )->addQueryPart(
            $norPartSix
        );
        $nor->addBuilder($secondNor);
        $queryBuilder->addQueryPart($nor);

        $finalQuery = $queryBuilder->getArrayCopy();

        $this->assertArrayHasKey('$or', $finalQuery);
        $this->assertArrayHasKey('$nor', $finalQuery);
        $this->assertCount(3, $finalQuery['$or']);
        $this->assertCount(2, $finalQuery['$nor']);
        $this->assertArrayHasKey('document.refId', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('document.appId', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('spam', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('cd', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('class', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('source', $finalQuery['$or'][0]);
        $this->assertArrayHasKey('encrypted', $finalQuery['$or'][0]);

        $this->assertArrayHasKey('document.refId', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('document.appId', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('spam', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('cd', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('class', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('source', $finalQuery['$or'][1]);
        $this->assertArrayHasKey('encrypted', $finalQuery['$or'][1]);

        $this->assertArrayHasKey('document.refId', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('document.appId', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('spam', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('cd', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('class', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('source', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('encrypted', $finalQuery['$or'][2]);
        $this->assertArrayHasKey('scanState', $finalQuery['$or'][2]);

        $this->assertArrayHasKey('price', $finalQuery['$nor'][0]);
        $this->assertArrayHasKey('$ne', $finalQuery['$nor'][0]['price']);
        $this->assertArrayHasKey('$exists', $finalQuery['$nor'][0]['price']);
        $this->assertArrayHasKey('$eq', $finalQuery['$nor'][0]['price']);
        $this->assertEquals(0.0, $finalQuery['$nor'][0]['price']['$ne']);
        $this->assertTrue($finalQuery['$nor'][0]['price']['$exists']);
        $this->assertFalse($finalQuery['$nor'][0]['price']['$eq']);

        $this->assertArrayHasKey('cancelled', $finalQuery['$nor'][1]);
        $this->assertArrayHasKey('refunded', $finalQuery['$nor'][1]);
        $this->assertArrayHasKey('deleted', $finalQuery['$nor'][1]);
        $this->assertArrayHasKey('$eq', $finalQuery['$nor'][1]['cancelled']);
        $this->assertArrayHasKey('$eq', $finalQuery['$nor'][1]['refunded']);
        $this->assertArrayHasKey('$eq', $finalQuery['$nor'][1]['deleted']);
        $this->assertTrue($finalQuery['$nor'][1]['cancelled']['$eq']);
        $this->assertTrue($finalQuery['$nor'][1]['refunded']['$eq']);
        $this->assertTrue($finalQuery['$nor'][1]['deleted']['$eq']);

        $dtNewestOne = $finalQuery['$or'][0]['cd']['$lte'];
        $dtOldestOne = $finalQuery['$or'][0]['cd']['$gte'];
        /* @var $dtNewest UTCDateTime */
        /* @var $dtOldestOne UTCDateTime */
        $this->assertNull($finalQuery['$or'][0]['document.refId']['$ne']);
        $this->assertNull($finalQuery['$or'][0]['document.appId']['$ne']);
        $this->assertFalse($finalQuery['$or'][0]['spam']['$eq']);
        $this->assertEquals($dateFrom->getTimestamp(), Date::mongo()->convertFrom($dtOldestOne)->getTimestamp());
        $this->assertEquals($dateTo->getTimestamp(), Date::mongo()->convertFrom($dtNewestOne)->getTimestamp());
        $this->assertEquals([$idOne, $idTwo], $finalQuery['$or'][0]['class']['$nin']);
        $this->assertEquals('user_submitted', $finalQuery['$or'][0]['source']['$eq']);
        $this->assertFalse($finalQuery['$or'][0]['encrypted']['$exists']);

        $dtNewestTwo = $finalQuery['$or'][1]['cd']['$lte'];
        $dtOldestTwo = $finalQuery['$or'][1]['cd']['$gte'];
        /* @var $dtNewestTwo UTCDateTime */
        /* @var $dtOldestTwo UTCDateTime */
        $this->assertNull($finalQuery['$or'][1]['document.refId']['$ne']);
        $this->assertNull($finalQuery['$or'][1]['document.appId']['$ne']);
        $this->assertFalse($finalQuery['$or'][1]['spam']['$eq']);
        $this->assertEquals($dateFrom->getTimestamp(), Date::mongo()->convertFrom($dtOldestTwo)->getTimestamp());
        $this->assertEquals($dateTo->getTimestamp(), Date::mongo()->convertFrom($dtNewestTwo)->getTimestamp());
        $this->assertEquals([$idOne, $idTwo], $finalQuery['$or'][1]['class']['$nin']);
        $this->assertEquals('user_submitted', $finalQuery['$or'][1]['source']['$eq']);
        $this->assertTrue($finalQuery['$or'][1]['encrypted']['$eq']);

        $dtNewestThree = $finalQuery['$or'][2]['cd']['$lte'];
        $dtOldestThree = $finalQuery['$or'][2]['cd']['$gte'];
        /* @var $dtNewestThree UTCDateTime */
        /* @var $dtOldestThree UTCDateTime */
        $this->assertNull($finalQuery['$or'][2]['document.refId']['$ne']);
        $this->assertNull($finalQuery['$or'][2]['document.appId']['$ne']);
        $this->assertFalse($finalQuery['$or'][2]['spam']['$eq']);
        $this->assertEquals($dateFrom->getTimestamp(), Date::mongo()->convertFrom($dtOldestThree)->getTimestamp());
        $this->assertEquals($dateTo->getTimestamp(), Date::mongo()->convertFrom($dtNewestThree)->getTimestamp());
        $this->assertEquals([$idOne, $idTwo], $finalQuery['$or'][2]['class']['$nin']);
        $this->assertEquals('user_submitted', $finalQuery['$or'][2]['source']['$eq']);
        $this->assertFalse($finalQuery['$or'][2]['encrypted']['$eq']);
        $this->assertEquals('scanned', $finalQuery['$or'][2]['scanState']['$eq']);
    }
}
