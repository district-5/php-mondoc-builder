<?php
namespace District5Tests\MondocBuilderTests;

use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\AndOperator;
use District5\MondocBuilder\QueryTypes\GeospatialPointNear;
use District5\MondocBuilder\QueryTypes\GeospatialPointNearSphere;
use District5\MondocBuilder\QueryTypes\HasAllValues;
use District5\MondocBuilder\QueryTypes\SizeOfValue;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueGreaterThan;
use District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueInValues;
use District5\MondocBuilder\QueryTypes\ValueLessThan;
use District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotInValues;
use District5\MondocBuilder\QueryTypes\OrOperator;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryBuilderTest
 * @package District5\MondocBuilderTests
 */
class QueryBuilderTest extends TestCase
{
    public function testBasicKeyEqualityBuilder()
    {
        $builder = QueryBuilder::get();

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

        $size = new SizeOfValue();
        $size->equals('numbers', 3);
        $builder->addQueryPart($size);

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

        $this->assertArrayHasKey('$eq', $query['firstName']);
        $this->assertArrayHasKey('$ne', $query['lastName']);
        $this->assertArrayHasKey('$lt', $query['age']);
        $this->assertArrayHasKey('$gt', $query['position']);
        $this->assertArrayHasKey('$lte', $query['numberFilms']);
        $this->assertArrayHasKey('$gte', $query['numberBooks']);

        $this->assertEquals('Joe', $query['firstName']['$eq']);
        $this->assertEquals('Bloggs', $query['lastName']['$ne']);
        $this->assertEquals(40, $query['age']['$lt']);
        $this->assertEquals(1.002, $query['position']['$gt']);
        $this->assertEquals(55, $query['numberFilms']['$lte']);
        $this->assertEquals(100, $query['numberBooks']['$gte']);
    }

    public function testBasicOrOperator()
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

        $or = new OrOperator();
        $or->addBuilder($builderOne)->addBuilder($builderTwo);

        $builderFinal = QueryBuilder::get();
        $builderFinal->addQueryPart($or);

        $query = $builderFinal->getArrayCopy();
        $this->assertArrayHasKey('$or', $query);
        $this->assertCount(2, $query['$or']);

        $this->assertArrayHasKey('firstName', $query['$or'][0]);
        $this->assertArrayHasKey('age', $query['$or'][0]);
        $this->assertArrayHasKey('firstName', $query['$or'][1]);
        $this->assertArrayHasKey('lastName', $query['$or'][1]);
        $this->assertArrayHasKey('$eq', $query['$or'][0]['firstName']);
        $this->assertArrayHasKey('$lt', $query['$or'][0]['age']);
        $this->assertArrayHasKey('$eq', $query['$or'][1]['firstName']);
        $this->assertArrayHasKey('$ne', $query['$or'][1]['lastName']);

        $this->assertEquals('Joe', $query['$or'][0]['firstName']['$eq']);
        $this->assertEquals(15, $query['$or'][0]['age']['$lt']);
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
    }

    public function testGeoSpatialNear()
    {
        $builder = QueryBuilder::get();
        $geo = new GeospatialPointNear();
        $geo->withinXMetresOfCoordinates('myLocation', 40, 1.22, 2.33);
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
        $this->assertEquals(40, $query['myLocation']['$near']['$maxDistance']);
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

        $final = $builder->getArrayCopy();
        $this->assertArrayHasKey('name', $final);
        $this->assertArrayHasKey('num', $final);
        $this->assertArrayHasKey('town', $final);
        $this->assertEquals('Jane', $final['name']);
        $this->assertEquals(456, $final['num']);
    }
}
