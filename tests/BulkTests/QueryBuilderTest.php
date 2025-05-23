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
use District5\MondocBuilder\QueryOptions;
use District5\MondocBuilder\QueryOptionsProjection;
use District5\MondocBuilder\QueryTypes\AndOperator;
use District5\MondocBuilder\QueryTypes\GeospatialPointNear;
use District5\MondocBuilder\QueryTypes\GeospatialPointNearSphere;
use District5\MondocBuilder\QueryTypes\HasAllValues;
use District5\MondocBuilder\QueryTypes\KeyExists;
use District5\MondocBuilder\QueryTypes\OrOperator;
use District5\MondocBuilder\QueryTypes\RegexMatch;
use District5\MondocBuilder\QueryTypes\SizeOfValue;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueGreaterThan;
use District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueInValues;
use District5\MondocBuilder\QueryTypes\ValueLessThan;
use District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotInValues;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryBuilderTest.
 *
 * @package District5\MondocBuilderTests\BulkTests
 *
 * @internal
 */
class QueryBuilderTest extends TestCase
{
    public function testBasicKeyEqualityBuilder()
    {
        $builder = QueryBuilder::get();
        $this->assertEmpty($builder->getArrayCopy());

        $exists = new KeyExists();
        $exists->true('firstName');
        $builder->addQueryPart($exists);

        $eq = new ValueEqualTo();
        $eq->string('firstName', 'Joe');
        $builder->addQueryPart($eq);

        $ne = new ValueNotEqualTo();
        $ne->string('lastName', 'Bloggs');
        $builder->addQueryPart($ne);

        $lt = new ValueLessThan();
        $lt->integer('age', 40);
        $builder->addQueryPart($lt);

        $gt = new ValueGreaterThan();
        $gt->float('position', 1.002);
        $builder->addQueryPart($gt);

        $gt = new ValueGreaterThan();
        $gt->double('position', 1.002);
        $builder->addQueryPart($gt);

        $lte = new ValueLessThanOrEqualTo();
        $lte->integer('numberFilms', 55);
        $builder->addQueryPart($lte);

        $gte = new ValueGreaterThanOrEqualTo();
        $gte->integer('numberBooks', 100);
        $builder->addQueryPart($gte);

        $in = new ValueInValues();
        $in->anArray('keywords', ['php', 'code']);
        $builder->addQueryPart($in);

        $nin = new ValueNotInValues();
        $nin->anArray('tags', ['foo', 'bar']);
        $builder->addQueryPart($nin);

        $all = new HasAllValues();
        $all->anArray('numbers', [1, 2, 3]);
        $builder->addQueryPart($all);

        $regex = RegexMatch::get()->regex(
            'preferredLanguage',
            '^en',
            'i' // default is ''
        );
        $builder->addQueryPart($regex);

        $size = new SizeOfValue();
        $size->equals('numbers', 3);
        $builder->addQueryPart($size);

        $anId = new ObjectId();
        $obj = new ValueEqualTo();
        $obj->objectId('identifier', $anId);
        $builder->addQueryPart($obj);

        $lessThanInt = new ValueLessThan();
        $lessThanInt->integer('age', 40);
        $builder->addQueryPart($lessThanInt);

        $greaterThanInt = new ValueGreaterThan();
        $greaterThanInt->integer('age', 20);
        $builder->addQueryPart($greaterThanInt);

        $dateTime = Date::mongo()->convertTo(
            Date::createYMDHISM(2021, 1, 1, 0, 0, 0)
        );
        $dt = new ValueEqualTo();
        $dt->utcDateTime('created_at', $dateTime);
        $builder->addQueryPart($dt);

        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('firstName', $query);
        $this->assertArrayHasKey('lastName', $query);
        $this->assertArrayHasKey('age', $query);
        $this->assertArrayHasKey('position', $query);
        $this->assertArrayHasKey('numberFilms', $query);
        $this->assertArrayHasKey('numberBooks', $query);

        $this->assertArrayHasKey('keywords', $query);
        $this->assertIsArray($query['keywords']);
        $this->assertArrayHasKey('$in', $query['keywords']);
        $this->assertCount(2, $query['keywords']['$in']);

        $this->assertArrayHasKey('tags', $query);
        $this->assertIsArray($query['tags']);
        $this->assertArrayHasKey('$nin', $query['tags']);
        $this->assertCount(2, $query['tags']['$nin']);

        $this->assertArrayHasKey('numbers', $query);
        $this->assertIsArray($query['numbers']);
        $this->assertArrayHasKey('$all', $query['numbers']);
        $this->assertArrayHasKey('$size', $query['numbers']);
        $this->assertCount(3, $query['numbers']['$all']);
        $this->assertEquals(3, $query['numbers']['$size']);

        $this->assertArrayHasKey('preferredLanguage', $query);
        $this->assertArrayHasKey('$regex', $query['preferredLanguage']);
        $this->assertArrayHasKey('$options', $query['preferredLanguage']);
        $this->assertEquals('^en', $query['preferredLanguage']['$regex']);
        $this->assertEquals('i', $query['preferredLanguage']['$options']);

        $this->assertArrayHasKey('$eq', $query['firstName']);
        $this->assertArrayHasKey('$ne', $query['lastName']);
        $this->assertArrayHasKey('$lt', $query['age']);
        $this->assertArrayHasKey('$gt', $query['position']);
        $this->assertArrayHasKey('$lte', $query['numberFilms']);
        $this->assertArrayHasKey('$gte', $query['numberBooks']);

        $this->assertTrue($query['firstName']['$exists']);
        $this->assertEquals('Joe', $query['firstName']['$eq']);
        $this->assertEquals('Bloggs', $query['lastName']['$ne']);
        $this->assertEquals(40, $query['age']['$lt']);
        $this->assertEquals(1.002, $query['position']['$gt']);
        $this->assertEquals(55, $query['numberFilms']['$lte']);
        $this->assertEquals(100, $query['numberBooks']['$gte']);

        $this->assertEquals($anId, $query['identifier']['$eq']);

        $this->assertEquals($dateTime, $query['created_at']['$eq']);

        $this->assertEquals(40, $query['age']['$lt']);
        $this->assertEquals(20, $query['age']['$gt']);
    }

    public function testBasicOrOperator()
    {
        $builderOne = QueryBuilder::get();

        $existsOne = new KeyExists();
        $existsOne->true('firstName');
        $builderOne->addQueryPart($existsOne);

        $eqOne = new ValueEqualTo();
        $eqOne->string('firstName', 'Joe');
        $builderOne->addQueryPart($eqOne);

        $ltOne = new ValueLessThan();
        $ltOne->integer('age', 15);
        $builderOne->addQueryPart($ltOne);

        $builderTwo = QueryBuilder::get();

        $existsTwo = new KeyExists();
        $existsTwo->false('firstName');
        $builderTwo->addQueryPart($existsTwo);

        $eqTwo = new ValueEqualTo();
        $eqTwo->string('firstName', 'Jane');
        $builderTwo->addQueryPart($eqTwo);
        $neTwo = new ValueNotEqualTo();
        $neTwo->string('lastName', 'Bloggs');
        $builderTwo->addQueryPart($neTwo);

        $or = new OrOperator();
        $or->addBuilder($builderOne)->addBuilder($builderTwo);

        $builderFinal = QueryBuilder::get();
        $builderFinal->addQueryPart($or);

        $query = $builderFinal->getArrayCopy();
        $this->assertArrayHasKey('$or', $query);
        $this->assertCount(2, $query['$or']);

        $this->assertArrayHasKey('firstName', $query['$or'][0]);
        $this->assertArrayHasKey('$exists', $query['$or'][0]['firstName']);
        $this->assertArrayHasKey('age', $query['$or'][0]);
        $this->assertArrayHasKey('$exists', $query['$or'][1]['firstName']);
        $this->assertArrayHasKey('firstName', $query['$or'][1]);
        $this->assertArrayHasKey('lastName', $query['$or'][1]);
        $this->assertArrayHasKey('$eq', $query['$or'][0]['firstName']);
        $this->assertArrayHasKey('$lt', $query['$or'][0]['age']);
        $this->assertArrayHasKey('$eq', $query['$or'][1]['firstName']);
        $this->assertArrayHasKey('$ne', $query['$or'][1]['lastName']);

        $this->assertTrue($query['$or'][0]['firstName']['$exists']);
        $this->assertEquals('Joe', $query['$or'][0]['firstName']['$eq']);
        $this->assertEquals(15, $query['$or'][0]['age']['$lt']);
        $this->assertFalse($query['$or'][1]['firstName']['$exists']);
        $this->assertEquals('Jane', $query['$or'][1]['firstName']['$eq']);
        $this->assertEquals('Bloggs', $query['$or'][1]['lastName']['$ne']);
    }

    public function testBasicAndOperator()
    {
        $builderOne = QueryBuilder::get();
        $eqOne = new ValueEqualTo();
        $eqOne->string('firstName', 'Joe');

        $ltOne = new ValueLessThan();
        $ltOne->integer('age', 15);
        $builderOne->addQueryPart($eqOne)->addQueryPart($ltOne);

        $builderTwo = QueryBuilder::get();
        $eqTwo = new ValueEqualTo();
        $eqTwo->string('firstName', 'Jane');
        $neTwo = new ValueNotEqualTo();
        $neTwo->string('lastName', 'Bloggs');
        $builderTwo->addQueryPart($eqTwo)->addQueryPart($neTwo);

        $and = new AndOperator();
        $and->addBuilder($builderOne)->addBuilder($builderTwo);

        $builderFinal = QueryBuilder::get();
        $builderFinal->addQueryPart($and);

        $query = $builderFinal->getArrayCopy();
        $this->assertArrayHasKey('$and', $query);
        $this->assertCount(2, $query['$and']);

        $this->assertArrayHasKey('firstName', $query['$and'][0]);
        $this->assertArrayHasKey('age', $query['$and'][0]);
        $this->assertArrayHasKey('firstName', $query['$and'][1]);
        $this->assertArrayHasKey('lastName', $query['$and'][1]);
        $this->assertArrayHasKey('$eq', $query['$and'][0]['firstName']);
        $this->assertArrayHasKey('$lt', $query['$and'][0]['age']);
        $this->assertArrayHasKey('$eq', $query['$and'][1]['firstName']);
        $this->assertArrayHasKey('$ne', $query['$and'][1]['lastName']);

        $this->assertEquals('Joe', $query['$and'][0]['firstName']['$eq']);
        $this->assertEquals(15, $query['$and'][0]['age']['$lt']);
        $this->assertEquals('Jane', $query['$and'][1]['firstName']['$eq']);
        $this->assertEquals('Bloggs', $query['$and'][1]['lastName']['$ne']);
    }

    public function testOptions()
    {
        $builder = QueryBuilder::get();
        $eqOne = new ValueEqualTo();
        $eqOne->string('firstName', 'Joe');
        $builder->addQueryPart($eqOne);

        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('firstName', $query);
        $this->assertArrayHasKey('$eq', $query['firstName']);
        $this->assertEquals('Joe', $query['firstName']['$eq']);

        $options = $builder->getOptions();
        $this->assertEmpty($options->getArrayCopy());

        $options->setSkip(1);
        $export = $options->getArrayCopy();
        $this->assertNotEmpty($export);
        $this->assertArrayHasKey('skip', $export);
        $this->assertEquals(1, $export['skip']);

        $options->setLimit(2);
        $export = $options->getArrayCopy();
        $this->assertNotEmpty($export);
        $this->assertArrayHasKey('skip', $export);
        $this->assertEquals(1, $export['skip']);
        $this->assertArrayHasKey('limit', $export);
        $this->assertEquals(2, $export['limit']);

        $options->setSortBy(['name' => -1]);
        $export = $options->getArrayCopy();
        $this->assertNotEmpty($export);
        $this->assertArrayHasKey('skip', $export);
        $this->assertEquals(1, $export['skip']);
        $this->assertArrayHasKey('limit', $export);
        $this->assertEquals(2, $export['limit']);
        $this->assertArrayHasKey('sort', $export);
        $this->assertArrayHasKey('name', $export['sort']);
        $this->assertEquals(-1, $export['sort']['name']);

        $options->setCustom(['foo' => 'bar']);
        $export = $options->getArrayCopy();
        $this->assertNotEmpty($export);
        $this->assertArrayHasKey('skip', $export);
        $this->assertEquals(1, $export['skip']);
        $this->assertArrayHasKey('limit', $export);
        $this->assertEquals(2, $export['limit']);
        $this->assertArrayHasKey('sort', $export);
        $this->assertArrayHasKey('name', $export['sort']);
        $this->assertEquals(-1, $export['sort']['name']);
        $this->assertArrayHasKey('foo', $export);
        $this->assertEquals('bar', $export['foo']);

        $optionsSimple = new QueryOptions(new QueryBuilder());
        $optionsSimple->setSortBySimple('name', 1);
        $export = $optionsSimple->getArrayCopy();
        $this->assertArrayHasKey('sort', $export);
        $this->assertArrayHasKey('name', $export['sort']);
        $this->assertEquals(1, $export['sort']['name']);
    }

    public function testGeoSpatialNear()
    {
        $builder = QueryBuilder::get();
        $geo = new GeospatialPointNear();
        $geo->withinXMilesOfCoordinates('myLocation', 40, 1.22, 2.33);
        $builder->addQueryPart($geo);
        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('myLocation', $query);
        $this->assertArrayHasKey('$near', $query['myLocation']);
        $this->assertArrayHasKey('$geometry', $query['myLocation']['$near']);
        $this->assertArrayHasKey('$maxDistance', $query['myLocation']['$near']);
        $this->assertArrayHasKey('type', $query['myLocation']['$near']['$geometry']);
        $this->assertArrayHasKey('coordinates', $query['myLocation']['$near']['$geometry']);
        $this->assertCount(2, $query['myLocation']['$near']['$geometry']['coordinates']);
        $this->assertEquals(1.22, $query['myLocation']['$near']['$geometry']['coordinates'][0]);
        $this->assertEquals(2.33, $query['myLocation']['$near']['$geometry']['coordinates'][1]);
        $this->assertEquals(64374, $query['myLocation']['$near']['$maxDistance']);
    }

    public function testGeoSpatialNearSphere()
    {
        $builder = QueryBuilder::get();
        $geo = new GeospatialPointNearSphere();
        $geo->withinXMetresOfCoordinates('myLocation', 40, 1.22, 2.33);
        $builder->addQueryPart($geo);
        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('myLocation', $query);
        $this->assertArrayHasKey('$nearSphere', $query['myLocation']);
        $this->assertArrayHasKey('$geometry', $query['myLocation']['$nearSphere']);
        $this->assertArrayHasKey('$maxDistance', $query['myLocation']['$nearSphere']);
        $this->assertArrayHasKey('type', $query['myLocation']['$nearSphere']['$geometry']);
        $this->assertArrayHasKey('coordinates', $query['myLocation']['$nearSphere']['$geometry']);
        $this->assertCount(2, $query['myLocation']['$nearSphere']['$geometry']['coordinates']);
        $this->assertEquals(1.22, $query['myLocation']['$nearSphere']['$geometry']['coordinates'][0]);
        $this->assertEquals(2.33, $query['myLocation']['$nearSphere']['$geometry']['coordinates'][1]);
        $this->assertEquals(40, $query['myLocation']['$nearSphere']['$maxDistance']);
    }

    public function testGeoSpatialNearSphereFromStatic()
    {
        $builder = QueryBuilder::get();
        $geo = GeospatialPointNearSphere::get();
        $geo->withinXMetresOfCoordinates('myLocation', 40, -0.1415681, 51.5006761);
        $builder->addQueryPart($geo);
        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('myLocation', $query);
        $this->assertArrayHasKey('$nearSphere', $query['myLocation']);
        $this->assertArrayHasKey('$geometry', $query['myLocation']['$nearSphere']);
        $this->assertArrayHasKey('$maxDistance', $query['myLocation']['$nearSphere']);
        $this->assertArrayHasKey('type', $query['myLocation']['$nearSphere']['$geometry']);
        $this->assertArrayHasKey('coordinates', $query['myLocation']['$nearSphere']['$geometry']);
        $this->assertCount(2, $query['myLocation']['$nearSphere']['$geometry']['coordinates']);
        $this->assertEquals(-0.1415681, $query['myLocation']['$nearSphere']['$geometry']['coordinates'][0]);
        $this->assertEquals(51.5006761, $query['myLocation']['$nearSphere']['$geometry']['coordinates'][1]);
        $this->assertEquals(40, $query['myLocation']['$nearSphere']['$maxDistance']);
    }

    public function testCustomPartWithGeoSpatial()
    {
        $customOne = [
            'name' => 'Jane',
            'num' => 123,
            'town' => 'Joetown, USA'
        ];
        $customTwo = [
            'name' => 'Jane',
            'num' => 456
        ];
        $builder = QueryBuilder::get();

        $eq = new ValueEqualTo();
        $eq->string('name', 'Janet');
        $builder->addQueryPart($eq);

        $builder->addCustomArrayPart($customOne);
        $builder->addCustomArrayPart($customTwo);

        $builder->addCustomArrayPart([]);

        $final = $builder->getArrayCopy();
        $this->assertArrayHasKey('name', $final);
        $this->assertArrayHasKey('num', $final);
        $this->assertArrayHasKey('town', $final);
        $this->assertEquals('Jane', $final['name']);
        $this->assertEquals(456, $final['num']);
    }

    public function testQueryOptionsProjection()
    {
        $projection = new QueryOptionsProjection();
        $this->assertFalse($projection->has('foo'));
        $this->assertTrue($projection->isEmpty());
        $this->assertEmpty($projection->getArrayCopy());

        $projection->add('foo', 1);
        $this->assertTrue($projection->has('foo'));
        $this->assertFalse($projection->isEmpty());
        $this->assertArrayHasKey('foo', $projection->getArrayCopy());
        $this->assertEquals(1, $projection->getArrayCopy()['foo']);
    }

    public function testQueryOptionsProjectionInBuilder()
    {
        $builder = new QueryBuilder();
        $builder->addQueryPart(ValueEqualTo::get()->string('foo', 'bar'));
        $builder->getOptions()->getProjection()->add('foo', 1);

        $query = $builder->getArrayCopy();
        $this->assertArrayHasKey('foo', $query);
        $this->assertArrayHasKey('$eq', $query['foo']);
        $this->assertEquals('bar', $query['foo']['$eq']);

        $options = $builder->getOptions()->getArrayCopy();
        $this->assertArrayHasKey('projection', $options);
        $this->assertArrayHasKey('foo', $options['projection']);
        $this->assertEquals(1, $options['projection']['foo']);
    }
}
