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

namespace District5\MondocBuilder\QueryTypes;

use District5\MondocBuilder\QueryBuilder;
use District5\MondocBuilder\QueryTypes\Abstracts\AbstractQueryType;

/**
 * Class OrOperator.
 *
 * @package District5\MondocBuilder\QueryTypes
 */
class OrOperator extends AbstractQueryType
{
    /**
     * @var QueryBuilder[]
     */
    protected $parts = [];

    /**
     * Add a query to this $or operation.
     *
     * @param QueryBuilder $builder
     *
     * @return OrOperator
     */
    public function addBuilder(QueryBuilder $builder): OrOperator
    {
        $this->parts[] = $builder;

        return $this;
    }

    /**
     * Get the array version of this query part.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $base = ['$or' => []];
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->parts as $_ => $builder) {
            $base['$or'][] = $builder->getArrayCopy();
        }

        return $base;
    }
}
