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

namespace District5\MondocBuilder;

use District5\MondocBuilder\Exception\MondocBuilderInvalidParamException;

/**
 * Class QueryOptionsProjection.
 *
 * @package District5\MondocBuilder
 */
class QueryOptionsProjection implements AbstractExportableArray
{
    /**
     * @var array
     */
    private array $fields = [];

    /**
     * Check whether there is a specific field is contained in the projection
     *
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool
    {
        return array_key_exists($field, $this->fields);
    }

    /**
     * Get the value of a field in the projection
     *
     * @param string $field
     * @return int|null
     */
    public function get(string $field): int|null
    {
        if (!array_key_exists($field, $this->fields)) {
            return null;
        }
        return $this->fields[$field];
    }

    /**
     * Are there any fields in the projection?
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->fields);
    }

    /**
     * Add a field to the projection
     *
     * @param string $field The field to check
     * @param bool|int $included true/1 for included, 0/false for excluded
     * @return static
     */
    public function add(string $field, bool|int $included = true): static
    {
        if (is_bool($included)) {
            $included = (int)$included;
        }
        if ($included !== 0 && $included !== 1) {
            throw new MondocBuilderInvalidParamException(
                'Projection value must be either true, false, 1 or 0.'
            );
        }
        $this->fields[$field] = $included;
        return $this;
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function getArrayCopy(): array
    {
        return $this->fields;
    }
}
