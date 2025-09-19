<?php
namespace Ning\SDK\Services;

class RestService
{
    /** @var callable|null */
    private $handler;

    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
    }

    public function request(string $method, string $url, $params = null, $headers = null, $options = null)
    {
        if ($this->handler) {
            return call_user_func($this->handler, $method, $url, $params, $headers, $options);
        }
        return json_encode([
            'method' => $method,
            'url' => $url,
            'params' => $params,
        ]);
    }
}
