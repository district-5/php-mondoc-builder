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

namespace District5\MondocBuilder\QueryTypes\Abstracts;

/**
 * Class AbstractValueInNotInAll.
 *
 * @package District5\MondocBuilder\QueryTypes\Abstracts
 */
abstract class AbstractValueInNotInAll extends AbstractQueryType
{
    /**
     * Add an array of values for this key to match against.
     *
     * @param string $key
     * @param array $values
     *
     * @return $this
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function anArray(string $key, array $values)
    {
        $this->parts[$key] = $values;

        return $this;
    }

    /**
     * Export this query part as an array.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $base = [];
        foreach ($this->parts as $key => $parts) {
            // @var $parts array
            if (empty($parts)) {
                continue;
            }
            $base[$key] = [$this->getOperator() => $parts];
        }

        return $base;
    }

    /**
     * @return string
     */
    abstract protected function getOperator(): string;
}
