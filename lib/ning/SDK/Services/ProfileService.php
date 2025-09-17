<?php
namespace Ning\SDK\Services;

use Ning\SDK\Entities\Application;
use Ning\SDK\Entities\Profile;

class ProfileService
{
    /** @var Application */
    private $application;
    /** @var Profile[] keyed by screen name */
    private $profilesByScreenName = [];
    /** @var Profile[] keyed by email */
    private $profilesByEmail = [];
    /** @var Profile */
    private $currentProfile;
    /** @var string */
    private $profileClass;

    public function __construct(ApplicationService $applicationService, array $profiles = [], string $profileClass = Profile::class)
    {
        $this->application = $applicationService->load();
        $this->profileClass = $profileClass;
        $this->currentProfile = $this->instantiateProfile(['loggedIn' => false]);

        $ownerProfile = $this->instantiateProfile([
            'email' => $this->application->ownerName . '@example.com',
            'screenName' => $this->application->ownerName,
            'title' => $this->application->ownerName,
            'isOwner' => true,
            'createdDate' => date('c'),
        ]);
        $ownerProfile->setPassword('owner-password');
        $this->storeProfile($ownerProfile);

        foreach ($profiles as $profileConfig) {
            $this->create($profileConfig['email'], $profileConfig['password'] ?? 'changeme', $profileConfig);
        }
    }

    private function storeProfile(Profile $profile): void
    {
        $this->profilesByScreenName[strtolower($profile->screenName)] = $profile;
        $this->profilesByEmail[strtolower($profile->email)] = $profile;
    }

    public function create(string $email, string $password, array $attributes = []): Profile
    {
        $screenName = $attributes['screenName'] ?? $this->defaultScreenName($email);
        $profile = $this->instantiateProfile(array_merge($attributes, [
            'email' => $email,
            'screenName' => $screenName,
            'title' => $attributes['title'] ?? $screenName,
            'createdDate' => $attributes['createdDate'] ?? date('c'),
        ]));
        if (!empty($attributes['isOwner'])) {
            $profile->markOwner(true);
        }
        $profile->setPassword($password);
        $this->storeProfile($profile);
        return $profile;
    }

    public function load($identifier): ?Profile
    {
        if ($identifier instanceof Profile) {
            return $identifier;
        }
        if (is_string($identifier)) {
            if (isset($this->profilesByScreenName[strtolower($identifier)])) {
                return $this->profilesByScreenName[strtolower($identifier)];
            }
            if (isset($this->profilesByEmail[strtolower($identifier)])) {
                return $this->profilesByEmail[strtolower($identifier)];
            }
        }
        return null;
    }

    public function signIn(string $emailOrScreenName, string $password, array $options = [])
    {
        $profile = $this->load($emailOrScreenName);
        if (!$profile || !$profile->checkPassword($password)) {
            return ['errorCode' => 'INVALID_CREDENTIALS'];
        }
        if (!empty($options['max-age'])) {
            $profile->sessionExpiry = time() + (int)$options['max-age'];
        }
        $profile->markLoggedIn(true);
        $this->currentProfile = $profile;
        return true;
    }

    public function signOut(): void
    {
        if ($this->currentProfile) {
            $this->currentProfile->markLoggedIn(false);
        }
        $this->currentProfile = $this->instantiateProfile(['loggedIn' => false]);
    }

    public function current(): Profile
    {
        return $this->currentProfile;
    }

    private function defaultScreenName(string $email): string
    {
        $name = strstr($email, '@', true);
        if ($name === false) {
            $name = $email;
        }
        $base = preg_replace('/[^a-z0-9_]+/i', '-', strtolower($name));
        $candidate = $base;
        $suffix = 1;
        while (isset($this->profilesByScreenName[$candidate])) {
            $candidate = $base . $suffix;
            $suffix++;
        }
        return $candidate ?: 'user' . count($this->profilesByScreenName);
    }

    private function instantiateProfile(array $attributes): Profile
    {
        $class = $this->profileClass;
        return new $class($attributes);
    }
}
