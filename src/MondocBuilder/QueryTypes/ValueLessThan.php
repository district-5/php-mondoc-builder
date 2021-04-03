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

use District5\MondocBuilder\QueryTypes\Abstracts\AbstractValueEqualityParts;

/**
 * Class ValueLessThan.
 *
 * @package District5\MondocBuilder\QueryTypes
 */
class ValueLessThan extends AbstractValueEqualityParts
{
    /**
     * @return string
     */
    protected function getOperator(): string
    {
        return '$lt';
    }
}
