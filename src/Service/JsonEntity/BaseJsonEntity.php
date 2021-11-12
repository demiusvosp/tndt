<?php
/**
 * User: demius
 * Date: 13.11.2021
 * Time: 1:06
 */
declare(strict_types=1);

namespace App\Service\JsonEntity;

use App\Exception\ConversionException;
use ArrayAccess;
use JsonException;
use JsonSerializable;
use LogicException;
use Stringable;

abstract class BaseJsonEntity implements JsonSerializable, Stringable, ArrayAccess
{
    /**
     * @param string|array|BaseJsonEntity $arg
     */
    public function __construct($arg)
    {
        if (is_string($arg)) {
            $arg = json_decode($arg, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ConversionException($arg, 'JsonEntity array');
            }
        }
        if (!is_array($arg) && !$arg instanceof BaseJsonEntity) {
            throw new \InvalidArgumentException('Cannot create JsonEntity from ' . gettype($arg));
        }
        // Более конкретные реализации заполнят свои атрибуты
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws
     */
    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('JsonEntity does not unset attribute');
    }
}