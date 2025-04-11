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

namespace District5Tests\MondocBuilderTests;

use District5\Date\Date;
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\KeyExists;
use District5\MondocBuilder\QueryTypes\OrOperator;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use District5\MondocBuilder\QueryTypes\ValueGreaterThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueLessThanOrEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotEqualTo;
use District5\MondocBuilder\QueryTypes\ValueNotInValues;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

/**
 * Class LargeOrTest
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class QueryPartTest extends TestCase
{
    public function testValidQueryPart()
    {
        $greaterThan = ValueGreaterThanOrEqualTo::get()->integer('intKey', 1);
        $this->assertEquals(['intKey' => ['$gte' => 1]], $greaterThan->getArrayCopy());
    }

    /**
     * Can only be triggered by an extension to the QueryPart
     * @return void
     * @throws ReflectionException
     */
    public function testInvalidQueryPart(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $reflectionClass = new ReflectionClass(ValueGreaterThanOrEqualTo::class);
        // Create a new instance of the class
        $instance = $reflectionClass->newInstance();
        $reflectionMethod = $reflectionClass->getMethod('buildQueryParts');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($instance, 'intKey', 999); // This will throw an exception as 999 is not a valid type
    }
}
