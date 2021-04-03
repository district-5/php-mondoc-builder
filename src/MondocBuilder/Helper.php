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

use DateTime;
use MongoDB\BSON\UTCDateTime;

/**
 * Class Helper.
 *
 * @package District5\MondocBuilder
 */
class Helper
{
    /**
     * Convert a PHP DateTime to a Mongo UTCDateTime. It doesn't matter which you pass in.
     *
     * @param DateTime|UTCDateTime $provided
     *
     * @return null|UTCDateTime
     */
    public static function phpDateToMongoDateTime($provided): ?UTCDateTime
    {
        if (!is_object($provided)) {
            return null;
        }
        if ($provided instanceof UTCDateTime) {
            return $provided;
        }
        if ($provided instanceof DateTime) {
            return new UTCDateTime(($provided->format('Uv')));
        }

        return null;
    }
}
