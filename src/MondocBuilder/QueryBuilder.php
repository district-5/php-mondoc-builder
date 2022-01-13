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

use District5\MondocBuilder\QueryTypes\Abstracts\AbstractQueryType;

/**
 * Class QueryBuilder.
 *
 * @package District5\MondocBuilder
 */
class QueryBuilder implements AbstractExportableArray
{
    /**
     * @var AbstractQueryType[]
     */
    protected $commands = [];

    /**
     * @var array
     */
    protected $custom = [];

    /**
     * @var QueryOptions
     */
    private $options;

    /**
     * Get a new instance of the QueryBuilder.
     *
     * @return QueryBuilder
     * @noinspection PhpUnused
     */
    public static function get(): QueryBuilder
    {
        return new self();
    }

    /**
     * Add a query part to this builder instance.
     *
     * @param AbstractQueryType $part
     *
     * @return $this
     */
    public function addQueryPart(AbstractQueryType $part): QueryBuilder
    {
        $this->commands[] = $part;

        return $this;
    }

    /**
     * Get the final database query array.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $final = [];
        foreach ($this->commands as $command) {
            $array = $command->getArrayCopy();
            foreach ($array as $k => $v) {
                if (!array_key_exists($k, $final)) {
                    $final[$k] = [];
                }
                foreach ($v as $a => $b) {
                    $final[$k][$a] = $b;
                }
            }
        }
        if (!empty($this->custom)) {
            $tmp = array_merge([], $final);
            foreach ($this->custom as $item) {
                if (empty($item)) {
                    continue;
                }
                $tmp = array_merge($tmp, $item);
            }

            return $tmp;
        }

        return $final;
    }

    /**
     * Get the options.
     *
     * @return QueryOptions
     */
    public function getOptions(): QueryOptions
    {
        if (null === $this->options) {
            $this->options = new QueryOptions($this);
        }

        return $this->options;
    }

    /**
     * @param QueryOptions $options
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function setOptions(QueryOptions $options): QueryBuilder
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $custom
     *
     * @return QueryBuilder
     */
    public function addCustomArrayPart(array $custom): QueryBuilder
    {
        $this->custom[] = $custom;

        return $this;
    }
}
