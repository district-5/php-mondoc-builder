<?php

/**
 * District5 - MondocBuilder
 *
 *  - A MongoDB query building library.
 *
 * @copyright District5
 *
 * @author District5
 * @link https://www.district5.co.uk
 *
 * @license This software and associated documentation (the "Software") may not be
 * used, copied, modified, distributed, published or licensed to any 3rd party
 * without the written permission of District5 or its author.
 *
 * The above copyright notice and this permission notice shall be included in
 * all licensed copies of the Software.
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
