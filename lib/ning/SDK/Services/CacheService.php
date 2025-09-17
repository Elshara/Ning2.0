<?php
namespace Ning\SDK\Services;

class CacheService
{
    private $store = [];

    public function get(string $key)
    {
        return $this->store[$key] ?? null;
    }

    public function insert(string $key, $value)
    {
        if (!array_key_exists($key, $this->store)) {
            $this->store[$key] = $value;
        }
        return $this->store[$key];
    }

    public function put(string $key, $value)
    {
        $this->store[$key] = $value;
        return $value;
    }

    public function remove(string $key): void
    {
        unset($this->store[$key]);
    }

    public function clear(): void
    {
        $this->store = [];
    }
}
