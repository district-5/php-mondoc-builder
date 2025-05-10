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

use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryOptions;
use District5\MondocBuilder\QueryTypes\ValueEqualTo;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryOptionsTest.
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class QueryOptionsTest extends TestCase
{
    public function testQueryOptions()
    {
        $eq = ValueEqualTo::get()->string('name', 'Joe Bloggs');
        $builder = QueryBuilder::get()->addQueryPart($eq);

        $options = new QueryOptions($builder);
        $options->setLimit(2);
        $options->setSkip(1);
        $options->setSortBy(['name' => 1]);

        $projection = $options->getProjection();
        $projection->add('name', 1);

        $this->assertEquals(
            [
                'limit' => 2,
                'skip' => 1,
                'sort' => [
                    'name' => 1,
                ],
                'projection' => [
                    'name' => 1,
                ],
            ],
            $options->getArrayCopy()
        );

        $options->setCustom([
            'this-is-custom' => 'value',
        ]);

        $this->assertEquals(
            [
                'limit' => 2,
                'skip' => 1,
                'sort' => [
                    'name' => 1,
                ],
                'this-is-custom' => 'value',
                'projection' => [
                    'name' => 1,
                ],
            ],
            $options->getArrayCopy()
        );

        $options->setSortBySimple('foo', -1);

        $this->assertEquals(
            [
                'limit' => 2,
                'skip' => 1,
                'sort' => [
                    'foo' => -1,
                ],
                'this-is-custom' => 'value',
                'projection' => [
                    'name' => 1,
                ],
            ],
            $options->getArrayCopy()
        );
    }
}
