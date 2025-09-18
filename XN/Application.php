<?php
class XN_Application
{
    public $relativeUrl = 'sample-app';
    public $name = 'Sample App';
    public $ownerName = 'owner';

    public static function load(): self
    {
        return new self();
    }

    public function iconUrl(): string
    {
        return '';
    }
}
