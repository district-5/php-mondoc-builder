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

/**
 * Class QueryOptions.
 *
 * @package District5\MondocBuilder
 */
class QueryOptions implements AbstractExportableArray
{
    /**
     * @var array
     */
    private $sortBy = [];

    /**
     * @var null|int
     */
    private $skip;

    /**
     * @var null|int
     */
    private $limit;

    /**
     * @var array
     */
    private $custom = [];

    /**
     * QueryOptions constructor.
     *
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder)
    {
        $builder->setOptions($this);
    }

    /**
     * @param null|int $skip
     *
     * @return QueryOptions
     * @noinspection PhpUnused
     */
    public function setSkip(?int $skip): QueryOptions
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * @param null|int $limit
     *
     * @return QueryOptions
     * @noinspection PhpUnused
     */
    public function setLimit(?int $limit): QueryOptions
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param array $sortBy
     *
     * @return QueryOptions
     * @noinspection PhpUnused
     */
    public function setSortBy(array $sortBy): QueryOptions
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @param string $field
     * @param int $direction
     *
     * @return QueryOptions
     * @noinspection PhpUnused
     */
    public function setSortBySimple(string $field, int $direction): QueryOptions
    {
        $this->sortBy = [$field => $direction];

        return $this;
    }

    /**
     * @param array $customOptions
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function setCustom(array $customOptions): QueryOptions
    {
        $this->custom = $customOptions;

        return $this;
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function getArrayCopy(): array
    {
        $opts = [];
        if (!empty($this->sortBy)) {
            $opts = array_merge($opts, ['sort' => $this->sortBy]);
        }
        if (null !== $this->skip) {
            $opts['skip'] = $this->skip;
        }
        if (null !== $this->limit) {
            $opts['limit'] = $this->limit;
        }
        if (!empty($this->custom)) {
            return array_merge($opts, $this->custom);
        }

        return $opts;
    }
}
