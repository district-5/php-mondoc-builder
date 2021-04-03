District5 - MondocBuilder
======

#### A MongoDB query building library.

### Composer...

In your `composer.json` file include:

```json
{
    "repositories":[
        {
            "type": "vcs",
            "url": "git@github.com:district-5/php-mondoc-builder.git"
        }
    ],
    "require": {
        "php": ">=7.1",
        "district5/mondoc-builder": ">=3.5.6",
        "mongodb/mongodb": "^1.5",
        "ext-mongodb": "*"
    },
    "autoload" : {
        "psr-0" : {
            "MyNs" : "lib/"
        }
    }
}
```

### Testing and fixing...

* Install dependencies:
  ```
  $ composer install
  ```
* Run PHPUnit
  ```
  $ ./vendor/bin/phpunit
  ```
* Run PHP-CS-Fixer (automatically fix PHP code style issues)
  ```
  $ ./vendor/bin/php-cs-fixer fix
  ```

### Building a query...

The core function of this library is to build queries that can be used in Mongo.

Getting an instance of QueryBuilder

```php
use District5\MondocBuilder\QueryBuilder;

$builder = QueryBuilder::get(); // or $builder = new \District5\MondocBuilder\QueryBuilder();
```

Assigning options to the QueryBuilder (skip / limit / sort and custom options)

```php
<?php
use District5\MondocBuilder\QueryBuilder;

$builder = QueryBuilder::get();
$options = $builder->getOptions();
// Add some parts
// ...
// ...
$options->setLimit(10); // retrieve 10 document
$options->setSkip(10); // skip the first 10 documents
$options->setSortBy(
    ['$score' => ['$meta' => 'textScore']]
); // sort by name ascending.
// Or you can use setSortBySimple for simple sorting. `$options->setSortBySimple('name', 1);`
$options->setCustom(
    [
        'customOpt1' => 'value',
        'customOpt2' => 'value'
    ]
);

// retrieve the final options array
$optionsForQuery = $options->getArrayCopy();
```

#### A query builder contains parts...

```php
<?php
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;

$builder = QueryBuilder::get();

$builder->addQueryPart(
    ValueEqualTo::get()->string('name', 'Joe')
);
$builder->addQueryPart(
    ValueNotEqualTo::get()->string('age', 18)
);

```

There are multiple `Part` types. These are listed below:
* `AndOperator` - `$and` - Add builders to this object create an `$and` query.
* `OrOperator` - `$or` - Add builders to this object to create an `$or` query.
* `ValueEqualTo` - `$eq` - A field is equal to a value.
* `ValueGreaterThan` - `$gt` - A field value is `>` than a provided value.
* `ValueGreaterThanOrEqualTo` - `$gte` -  A field value is `>=` a given value.
* `ValueInValues` - `$in` - A field value is in a list of values.
* `ValueNotInValues` - `$nin` - A field value is in a list of values.
* `ValueLessThan` - `$lt` - A field value is `<` than a provided value.
* `ValueLessThanOrEqualTo` - `$lte` - A field value is `<=` than a provided value.
* `ValueNotEqualTo` - `$ne` - A field value is `!=` a provided value.
* `GeospatialPointNear` - `$near` - A geospatial search.
* `GeospatialPointNearSphere` - `$nearSphere` - A geospatial search.
* `HasAllValues` - `$all` - A field value has all in list.
* `SizeOfValue` - `$size` - The size of a value equals this.

#### `$and` queries with the `AndOperator`

```php
<?php
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\AndOperator;

$and = new AndOperator();

$andBuilderOne = QueryBuilder::get();
// add parts
// ...
$andBuilderTwo = QueryBuilder::get();
// add parts
// ...

$and->addBuilder(
    $andBuilderOne
)->addBuilder(
    $andBuilderTwo
);

$builder = QueryBuilder::get();
$builder->addQueryPart($and);

$query = $builder->getArrayCopy();
$options = $builder->getOptions()->getArrayCopy();
```

### `$or` queries with the `OrOperator`

```php
<?php
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\OrOperator;

$orBuilderOne = QueryBuilder::get();
// add parts
// ...
$orBuilderTwo = QueryBuilder::get();
// add parts
// ...
$or = new OrOperator();
$or->addBuilder(
    $orBuilderOne
)->addBuilder(
    $orBuilderTwo
);
$builder = QueryBuilder::get();
$builder->addQueryPart($or);

$query = $builder->getArrayCopy();
$options = $builder->getOptions()->getArrayCopy();
```

### Simple equals example...

```php
<?php
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;

$builder = QueryBuilder::get();
// or new QueryBuilder();

// add a part
$part = new ValueNotEqualTo();
$part->string('name', 'Joe');
$builder->addQueryPart($part);

// you can inspect or get the final database query with:
$query = $builder->getArrayCopy();
// you can inspect or get the final database options part with:
$options = $builder->getOptions()->getArrayCopy();

// use $query and $options in the query on Mongo.
```

### More in depth example...

```php
<?php
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\GeospatialPointNear;use District5\MondocBuilder\QueryTypes\GeospatialPointNearSphere;use District5\MondocBuilder\QueryTypes\HasAllValues;use District5\MondocBuilder\QueryTypes\SizeOfValue;use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueGreaterThan;use District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo;use District5\MondocBuilder\QueryTypes\ValueInValues;use District5\MondocBuilder\QueryTypes\ValueLessThan;use District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo;use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;use District5\MondocBuilder\QueryTypes\ValueNotInValues;use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

$builder = QueryBuilder::get();

// basic equals
$equals = new ValueEqualTo();
$equals->string('aField', 'Joe');
$equals->integer('aField', 1);
$equals->boolean('aField', true);
$equals->dateTime('aField', new DateTime());
$equals->float('aField', 1.234);
$equals->double('aField', 1.234);
$equals->null('aField');

$equals->objectId('_id', new ObjectId()); // or you can use ->mongoNative
$equals->objectId('accountId', new ObjectId()); // or you can use ->mongoNative
$equals->mongoNative('aDate', new UTCDateTime());
$builder->addQueryPart($equals);

// basic NOT equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$notEqual = new ValueNotEqualTo();
$notEqual->integer('aField', 1);
$builder->addQueryPart($notEqual);

// $size queries
$size = new SizeOfValue();
$size->equals('aField', 2);
$builder->addQueryPart($size);

// greater than
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$greaterThan = new ValueGreaterThan();
$greaterThan->integer('aField', 1);
$builder->addQueryPart($greaterThan);

// greater than or equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$greaterThanOrEqualTo = new ValueGreaterThanOrEqualTo();
$greaterThanOrEqualTo->integer('aField', 1);
$builder->addQueryPart($greaterThanOrEqualTo);

// less than
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$lessThan = new ValueLessThan();
$lessThan->integer('aField', 1);
$builder->addQueryPart($lessThan);

// less than or equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$lessThanOrEqualTo = new ValueLessThanOrEqualTo();
$lessThanOrEqualTo->integer('aField', 1);
$builder->addQueryPart($lessThanOrEqualTo);

// value in queries - $in
$in = new ValueInValues();
$in->anArray('aField', ['foo', 'bar', 'dog', 'cat']);
$builder->addQueryPart($in);

// not in queries - $nin
$nin = new ValueNotInValues();
$nin->anArray('aField', ['foo', 'bar', 'dog', 'cat']);
$builder->addQueryPart($nin);

// geospatial query part - $nearSphere
$geo = new GeospatialPointNearSphere();
$geo->withinXMetresOfCoordinates('myLocation', 40, -0.1415681, 51.5006761); // Within 40 metres of Buckingham Palace
$geo->withinXMilesOfCoordinates('myLocation', 1, -0.1415681, 51.5006761); // Within 1 mile of Buckingham Palace
$builder->addQueryPart($geo);

// geospatial query part - $near
$geo = new GeospatialPointNear();
$geo->withinXMetresOfCoordinates('myLocation', 40, -0.1415681, 51.5006761); // Within 40 metres of Buckingham Palace
$geo->withinXMilesOfCoordinates('myLocation', 1, -0.1415681, 51.5006761); // Within 1 mile of Buckingham Palace
$builder->addQueryPart($geo);

// has all values
$hasAll = new HasAllValues();
$hasAll->anArray('field', ['my', 'possible', 'values']);
$builder->addQueryPart($hasAll);

// Custom query parts
$builder->addCustomArrayPart(
    [
        'aField' => 'FooBar',
        'anotherField' => 'Joe Bloggs'
    ]
);
$builder->addCustomArrayPart(
    [
        'andAnotherField' => 'ABC',
        'aFinalField' => 123
    ]
);

// you can inspect or get the final database query with:
$query = $builder->getArrayCopy();
// you can inspect or get the final database options part with:
$options = $builder->getOptions()->getArrayCopy();

// use $query and $options in the query on Mongo.

```
