<?php
namespace Ning\SDK\Services;

use Ning\SDK\Entities\Application;

class ApplicationService
{
    /** @var Application */
    private $application;

    public function __construct(array $config = [])
    {
        $defaults = [
            'name' => 'Example Network',
            'relativeUrl' => 'example',
            'ownerName' => 'example-owner',
            'premiumServices' => [
                'run-own-ads' => false,
                'private-source' => false,
            ],
            'iconUrl' => '/xn_resources/default/icon.png',
        ];
        $data = array_merge($defaults, $config);
        $this->application = new Application($data);
    }

    public function load(): Application
    {
        return $this->application;
    }

    public function update(array $attributes): void
    {
        $this->application->apply($attributes);
    }
}
