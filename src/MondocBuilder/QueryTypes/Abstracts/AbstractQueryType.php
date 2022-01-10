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

use District5\MondocBuilder\AbstractExportableArray;

/**
 * Class AbstractQueryType.
 *
 * @package District5\MondocBuilder\QueryTypes\Abstracts
 */
abstract class AbstractQueryType implements AbstractExportableArray
{
    /**
     * @var array
     */
    protected $parts = [];

    /**
     * Get an instance of this object from a static request.
     *
     * @return $this
     * @noinspection PhpMissingReturnTypeInspection
     * @example \District5\MondocBuilder\QueryTypes\ValueEqualTo::get();
     */
    public static function get()
    {
        $clz = get_called_class();

        return new $clz();
    }
}
