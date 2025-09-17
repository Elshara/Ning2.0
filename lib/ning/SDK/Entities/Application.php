<?php
namespace Ning\SDK\Entities;

class Application
{
    public $name;
    public $relativeUrl;
    public $ownerName;
    public $premiumServices;
    private $iconUrl;

    public function __construct(array $attributes)
    {
        $this->apply($attributes);
    }

    public function apply(array $attributes): void
    {
        $this->name = $attributes['name'] ?? $this->name;
        $this->relativeUrl = $attributes['relativeUrl'] ?? $this->relativeUrl;
        $this->ownerName = $attributes['ownerName'] ?? $this->ownerName;
        $this->premiumServices = $attributes['premiumServices'] ?? $this->premiumServices ?? [];
        $this->iconUrl = $attributes['iconUrl'] ?? $this->iconUrl ?? '/xn_resources/default/icon.png';
    }

    public function iconUrl($width = 64, $height = 64): string
    {
        return $this->iconUrl;
    }
}
