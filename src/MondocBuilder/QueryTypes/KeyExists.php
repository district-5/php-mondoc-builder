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

namespace District5\MondocBuilder\QueryTypes;

use District5\MondocBuilder\QueryTypes\Abstracts\AbstractQueryType;

/**
 * Class KeyExists.
 *
 * @package District5\MondocBuilder\QueryTypes
 */
class KeyExists extends AbstractQueryType
{
    /**
     * Adds an '$exists: false' check to a query.
     *
     * @param string $key
     * @return $this
     */
    public function false(string $key): KeyExists
    {
        $this->parts[$key] = false;

        return $this;
    }

    /**
     * Adds an '$exists: true' check to a query.
     *
     * @param string $key
     * @return $this
     */
    public function true(string $key): KeyExists
    {
        $this->parts[$key] = true;

        return $this;
    }

    /**
     * Get the array version of this query part.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        return array_map(function ($size) {
            return ['$exists' => $size];
        }, $this->parts);
    }
}
