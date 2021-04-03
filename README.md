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

* Getting an instance of QueryBuilder
  ```php
  $builder = \District5\MondocBuilder\QueryBuilder::get(); // or $builder = new \District5\MondocBuilder\QueryBuilder();
  ```
* Assigning options to the QueryBuilder (skip / limit / sort and custom options)
  ```php
  <?php
  $builder = \District5\MondocBuilder\QueryBuilder::get();
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

```php
<?php
$builder = \District5\MondocBuilder\QueryBuilder::get();
// or new \District5\MondocBuilder\QueryBuilder();

// add a part
$part = new \District5\MondocBuilder\QueryTypes\ValueNotEqualTo();
$part->string('name', 'Joe');
$builder->addQueryPart($part);

// you can inspect or get the final database query with:
$query = $builder->getArrayCopy();
// you can inspect or get the final database options part with:
$options = $builder->getOptions()->getArrayCopy();

// use $query and $options in the query on Mongo.
```

```php
<?php
$builder = \District5\MondocBuilder\QueryBuilder::get();

// basic equals
$equals = new \District5\MondocBuilder\QueryTypes\ValueEqualTo();
$equals->string('aField', 'Joe');
$equals->integer('aField', 1);
$equals->boolean('aField', true);
$equals->dateTime('aField', new DateTime());
$equals->float('aField', 1.234);
$equals->double('aField', 1.234);
$equals->null('aField');

$equals->objectId('_id', new \MongoDB\BSON\ObjectId()); // or you can use ->mongoNative
$equals->objectId('accountId', new \MongoDB\BSON\ObjectId()); // or you can use ->mongoNative
$equals->mongoNative('aDate', new \MongoDB\BSON\UTCDateTime());
$builder->addQueryPart($equals);

// basic NOT equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$notEqual = new \District5\MondocBuilder\QueryTypes\ValueNotEqualTo();
$notEqual->integer('aField', 1);
$builder->addQueryPart($notEqual);

// $size queries
$size = new \District5\MondocBuilder\QueryTypes\SizeOfValue();
$size->equals('aField', 2);
$builder->addQueryPart($size);

// greater than
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$greaterThan = new \District5\MondocBuilder\QueryTypes\ValueGreaterThan();
$greaterThan->integer('aField', 1);
$builder->addQueryPart($greaterThan);

// greater than or equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$greaterThanOrEqualTo = new \District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo();
$greaterThanOrEqualTo->integer('aField', 1);
$builder->addQueryPart($greaterThanOrEqualTo);

// less than
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$lessThan = new \District5\MondocBuilder\QueryTypes\ValueLessThan();
$lessThan->integer('aField', 1);
$builder->addQueryPart($lessThan);

// less than or equal to
// @todo inherits the same methods as the 'ValueEqualTo' part documented above.
$lessThanOrEqualTo = new \District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo();
$lessThanOrEqualTo->integer('aField', 1);
$builder->addQueryPart($lessThanOrEqualTo);

// value in queries - $in
$in = new \District5\MondocBuilder\QueryTypes\ValueInValues();
$in->anArray('aField', ['foo', 'bar', 'dog', 'cat']);
$builder->addQueryPart($in);

// not in queries - $nin
$nin = new \District5\MondocBuilder\QueryTypes\ValueNotInValues();
$nin->anArray('aField', ['foo', 'bar', 'dog', 'cat']);
$builder->addQueryPart($nin);

// geospatial query part - $nearSphere
$geo = new \District5\MondocBuilder\QueryTypes\GeospatialPointNearSphere();
$geo->withinXMetresOfCoordinates('myLocation', 40, -0.1415681, 51.5006761); // Within 40 metres of Buckingham Palace
$geo->withinXMilesOfCoordinates('myLocation', 1, -0.1415681, 51.5006761); // Within 1 mile of Buckingham Palace
$builder->addQueryPart($geo);

// geospatial query part - $near
$geo = new \District5\MondocBuilder\QueryTypes\GeospatialPointNear();
$geo->withinXMetresOfCoordinates('myLocation', 40, -0.1415681, 51.5006761); // Within 40 metres of Buckingham Palace
$geo->withinXMilesOfCoordinates('myLocation', 1, -0.1415681, 51.5006761); // Within 1 mile of Buckingham Palace
$builder->addQueryPart($geo);

// $or queries
$orBuilderOne = \District5\MondocBuilder\QueryBuilder::get();
$orBuilderTwo = \District5\MondocBuilder\QueryBuilder::get();
$or = new \District5\MondocBuilder\QueryTypes\OrOperator();
$or->addBuilder(
    $orBuilderOne
)->addBuilder(
    $orBuilderTwo
);
$builder->addQueryPart($or);

// has all values
$hasAll = new \District5\MondocBuilder\QueryTypes\HasAllValues();
$hasAll->anArray('field', ['my', 'possible', 'values']);
$builder->addQueryPart($hasAll);

// $and queries
$andBuilderOne = \District5\MondocBuilder\QueryBuilder::get();
$andBuilderTwo = \District5\MondocBuilder\QueryBuilder::get();
$and = new \District5\MondocBuilder\QueryTypes\AndOperator();
$and->addBuilder(
    $andBuilderOne
)->addBuilder(
    $andBuilderTwo
);
$builder->addQueryPart($and);

// Custom query parts
$builder->addCustomArrayPart(
    [
        'aField' => 'FooBar',
        'anotherField' => 'JoeBloggs'
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
