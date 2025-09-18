<?php
namespace Ning\SDK\Entities;

use ArrayIterator;
use IteratorAggregate;
use ArrayAccess;

class ContentAttributes implements ArrayAccess, IteratorAggregate
{
    private $data = [];

    public function set(string $name, $value, $type = null): self
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function raw(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __get($name)
    {
        return $this->raw($name);
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset($name): bool
    {
        return isset($this->data[$name]);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
