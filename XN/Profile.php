<?php
class XN_Profile
{
    public const FRIEND_PENDING = 'pending';
    public const GROUPIE = 'groupie';
    public const FRIEND = 'friend';
    public const NOT_FRIEND = 'not_friend';
    public const BLOCK = 'block';
    public const NOT_CONTACT = 'not_contact';
    public const PRESERVE_ASPECT_RATIO = -1;
    public const IS_VERIFIED = 'verified';

    public $screenName;
    public $email;

    public function __construct($screenName = 'demo', $email = 'demo@example.com')
    {
        $this->screenName = $screenName;
        $this->email = $email;
    }

    public static function current(): self
    {
        static $instance;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public static function load($screenName = null): self
    {
        return new self($screenName ?: 'user', 'user@example.com');
    }

    public function isLoggedIn(): bool
    {
        return true;
    }

    public function isOwner(): bool
    {
        return true;
    }

    public static function signOut(): void
    {
    }

    public function setContactStatus($screenNames, $relationship)
    {
        return true;
    }

    public function loginIsVerified(): bool
    {
        return true;
    }
}
