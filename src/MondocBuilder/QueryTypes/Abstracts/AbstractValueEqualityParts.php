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

namespace District5\MondocBuilder\QueryTypes\Abstracts;

use DateTime;
use District5\Date\Date;
use District5\MondocBuilder\Exception\MondocBuilderInvalidTypeException;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\MaxKey;
use MongoDB\BSON\MinKey;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;

/**
 * Class AbstractValueEqualityParts.
 *
 * @package District5\MondocBuilder\QueryTypes\Abstracts
 */
abstract class AbstractValueEqualityParts extends AbstractQueryType
{
    protected const TYPE_STRING = 0;
    protected const TYPE_INTEGER = 1;
    protected const TYPE_FLOAT = 2;
    protected const TYPE_BOOLEAN = 3;
    protected const TYPE_NULL = 4;
    protected const TYPE_BUILTIN = 5;
    protected const TYPE_DATETIME = 6;

    protected const SUPPORTED_NORMAL_TYPES = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_BOOLEAN,
        self::TYPE_NULL
    ];

    /**
     * Add a string value into this equality filter.
     *
     * @param string $key
     * @param string $string
     *
     * @return $this
     */
    public function string(string $key, string $string): static
    {
        return $this->addToQueryPart($key, $string, self::TYPE_STRING);
    }

    /**
     * Add an integer value into this equality filter.
     *
     * @param string $key
     * @param int $int
     *
     * @return $this
     */
    public function integer(string $key, int $int): static
    {
        return $this->addToQueryPart($key, $int, self::TYPE_INTEGER);
    }

    /**
     * Add a float value into this equality filter.
     *
     * @param string $key
     * @param float $float
     *
     * @return $this
     */
    public function float(string $key, float $float): static
    {
        return $this->addToQueryPart($key, $float, self::TYPE_FLOAT);
    }

    /**
     * Add a float value into this equality filter.
     *
     * @param string $key
     * @param float $double
     *
     * @return $this
     *
     * @see          AbstractValueEqualityParts::float()
     * @noinspection PhpUnused
     */
    public function double(string $key, float $double): static
    {
        return $this->float($key, $double);
    }

    /**
     * Add a boolean value into this equality filter.
     *
     * @param string $key
     * @param bool $bool
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function boolean(string $key, bool $bool): static
    {
        return $this->addToQueryPart($key, $bool, self::TYPE_BOOLEAN);
    }

    /**
     * Add a null value into this equality filter.
     *
     * @param string $key
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function null(string $key): static
    {
        return $this->addToQueryPart($key, null, self::TYPE_NULL);
    }

    /**
     * Add a native Mongo object into this equality filter.
     *
     * @param string $key
     * @param Binary|Decimal128|Javascript|MaxKey|MinKey|ObjectId|Regex|Timestamp|UTCDateTime $object
     *
     * @return $this
     */
    public function mongoNative(string $key, mixed $object): static
    {
        return $this->addToQueryPart($key, $object, self::TYPE_BUILTIN);
    }

    /**
     * Add an ObjectId part into this equality filter.
     *
     * @param string $key
     * @param ObjectId $objectId
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function objectId(string $key, ObjectId $objectId): static
    {
        return $this->mongoNative($key, $objectId);
    }

    /**
     * @param string $key
     * @param DateTime $dateTime
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function dateTime(string $key, DateTime $dateTime): static
    {
        return $this->addToQueryPart($key, $dateTime, self::TYPE_DATETIME);
    }

    /**
     * @param string $key
     * @param UTCDateTime $dateTime
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function utcDateTime(string $key, UTCDateTime $dateTime): static
    {
        return $this->mongoNative($key, $dateTime);
    }

    /**
     * Get the array version of this query part.
     *
     * @return array
     */
    public function getArrayCopy(): array
    {
        $base = [];
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->parts as $_ => $parts) {
            $key = $parts[0];
            if (!array_key_exists($key, $base)) {
                $base[$key] = [];
            }

            $base[$key] = array_merge($base[$key], $this->buildQueryParts($parts[1], $parts[2]));
        }

        return $base;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $type
     * @return $this
     */
    protected function addToQueryPart(string $key, mixed $value, int $type): static
    {
        $this->parts[] = [$key, $value, $type];
        return $this;
    }

    /**
     * @param mixed $value
     * @param int $variableType
     *
     * @return array
     * @throws MondocBuilderInvalidTypeException
     */
    protected function buildQueryParts(mixed $value, int $variableType): array
    {
        if (self::TYPE_BUILTIN === $variableType) {
            return [$this->getOperator() => $value];
        } else if (self::TYPE_DATETIME === $variableType) {
            return [$this->getOperator() => Date::mongo()->convertTo($value)];
        } else if (in_array($variableType, self::SUPPORTED_NORMAL_TYPES, true)) {
            return [$this->getOperator() => $value];
        }

        throw new MondocBuilderInvalidTypeException(
            sprintf('Invalid type passed in parts array, received: "%s".', $variableType)
        );
    }

    /**
     * @return string
     */
    abstract protected function getOperator(): string;
}
