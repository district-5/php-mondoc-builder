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
     * Get a new instance of the QueryBuilder.
     *
     * @return QueryBuilder
     */
    public static function get(): QueryBuilder
    {
        return new self();
    }

    /**
     * @param QueryOptions $options
     *
     * @return $this
     */
    public function setOptions(QueryOptions $options): QueryBuilder
    {
        $this->options = $options;

        return $this;
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
