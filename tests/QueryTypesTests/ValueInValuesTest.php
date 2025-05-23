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
use District5\MondocBuilder\QueryTypes\ValueInValues;
use District5Tests\MondocBuilderTests\TestQueryTypeAbstract;

/**
 * Class ValueInValuesTest
 *
 * @package District5\MondocBuilderTests
 *
 * @internal
 */
class ValueInValuesTest extends TestQueryTypeAbstract
{
    public function testQueryType()
    {
        $query = ValueInValues::get()->anArray(
            'k',
            [1, 2, 3]
        );
        $this->assertEquals(
            [
                'k' => [
                    '$in' => [1, 2, 3],
                ],
            ],
            $query->getArrayCopy()
        );
    }

    public function testQueryTypeWithBuilder()
    {
        $builder = QueryBuilder::get();
        $query = ValueInValues::get()->anArray(
            'k',
            [1, 2, 3]
        );
        $builder->addQueryPart($query);
        $this->assertEquals(
            [
                'k' => [
                    '$in' => [1, 2, 3],
                ],
            ],
            $builder->getArrayCopy()
        );
    }
}
