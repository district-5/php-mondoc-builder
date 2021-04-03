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

use District5\MondocBuilder\QueryTypes\Abstracts\AbstractQueryType;

/**
 * Class SizeOfValue.
 *
 * @package District5\MondocBuilder\QueryTypes
 */
class SizeOfValue extends AbstractQueryType
{
    /**
     * Adds a '$size' check to a query.
     *
     * @param string $key
     * @param int    $int
     *
     * @return $this
     */
    public function equals(string $key, int $int): SizeOfValue
    {
        $this->parts[$key] = $int;

        return $this;
    }

    /**
     * Get the array version of this query part.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $base = [];
        foreach ($this->parts as $key => $size) {
            $base[$key] = ['$size' => $size];
        }

        return $base;
    }
}
