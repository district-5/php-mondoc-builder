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

use District5\MondocBuilder\Exception\MondocBuilderInvalidParamException;
use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryOptionsProjection;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryOptionsProjectionTest.
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class QueryOptionsProjectionTest extends TestCase
{
    public function testProjection()
    {
        $projection = new QueryOptionsProjection();
        $this->assertTrue($projection->isEmpty());
        $projection->add('name', 1);
        $this->assertTrue($projection->has('name'));
        $this->assertEquals(1, $projection->get('name'));
        $projection->add('name', false);
        $this->assertEquals(0, $projection->get('name'));

        $this->assertFalse($projection->has('foobar'));
        $this->assertNull($projection->get('foobar'));
    }

    public function testProjectionViaSet()
    {
        $projection = new QueryOptionsProjection();
        $this->assertFalse($projection->has('name'));
        $projection->add('name', 1);
        $this->assertTrue($projection->has('name'));

        $options = QueryBuilder::get()->getOptions()->setProjection($projection);
        $this->assertTrue($options->hasProjection());
        $this->assertTrue($options->getProjection()->has('name'));
    }

    public function testInvalidProjectionValue()
    {
        $this->expectException(MondocBuilderInvalidParamException::class);
        $projection = new QueryOptionsProjection();
        $projection->add('foo', 2); // invalid value, should be true or false, or 1 or 0
    }
}
