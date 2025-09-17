<?php
namespace Ning\SDK\Entities;

class Profile
{
    public $email;
    public $screenName;
    public $title;
    public $createdDate;
    public $fullName;
    public $status;
    public $sessionExpiry;

    private $passwordHash;
    private $attributes = [];
    private $isOwner = false;
    private $loggedIn = false;

    public function __construct(array $attributes = [])
    {
        $this->apply($attributes);
    }

    public static function guest(): self
    {
        $profile = new self([
            'email' => null,
            'screenName' => null,
            'title' => null,
            'createdDate' => null,
        ]);
        $profile->loggedIn = false;
        return $profile;
    }

    public function apply(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                $this->attributes[$key] = $value;
            }
        }
        if (isset($attributes['isOwner'])) {
            $this->isOwner = (bool)$attributes['isOwner'];
        }
        if (isset($attributes['loggedIn'])) {
            $this->loggedIn = (bool)$attributes['loggedIn'];
        }
    }

    public function setPassword(string $password): void
    {
        $this->passwordHash = $this->hashPassword($password);
    }

    public function checkPassword(string $password): bool
    {
        if (!$this->passwordHash) {
            return false;
        }
        return hash_equals($this->passwordHash, $this->hashPassword($password));
    }

    private function hashPassword(string $password): string
    {
        return hash('sha256', $password);
    }

    public function markOwner(bool $owner): void
    {
        $this->isOwner = $owner;
    }

    public function markLoggedIn(bool $loggedIn): void
    {
        $this->loggedIn = $loggedIn;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    public function isOwner(): bool
    {
        return $this->isOwner;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return null;
    }

    public function __set($name, $value): void
    {
        $this->attributes[$name] = $value;
    }
}
