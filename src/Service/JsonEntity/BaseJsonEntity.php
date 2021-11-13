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

    public static function fromJson(?string $input): BaseJsonEntity
    {
        if ($input === null) {
            $input = '[]';
        }
        $input = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConversionException($input, 'JsonEntity array');
        }
        return new static($input);
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