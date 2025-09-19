<?php
require_once __DIR__ . '/../../lib/ning/bootstrap.php';
require_once __DIR__ . '/../../lib/XG_SecurityHelper.php';

use Ning\SDK\Environment;

function reset_environment(): void
{
    Environment::reset();
}

function assertTrue($condition, string $message): void
{
    if (!$condition) {
        throw new Exception($message);
    }
}

function assertEquals($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new Exception($message . " (expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ")");
    }
}

function testAuthenticationFlow(): void
{
    reset_environment();
    $profile = XN_Profile::create('alice@example.com', 'secret');
    assertEquals('alice', $profile->screenName, 'Screen name defaults to the mailbox portion of the email');
    $result = XN_Profile::signIn('alice@example.com', 'secret');
    assertTrue($result === true, 'Successful sign in should return true');
    $current = XN_Profile::current();
    assertTrue($current->isLoggedIn(), 'Current profile should be logged in after sign in');
    assertEquals($profile->screenName, $current->screenName, 'Current profile should match the signed in user');
    XG_SecurityHelper::assertIsXnProfile($current);
    XN_Profile::signOut();
    assertTrue(!XN_Profile::current()->isLoggedIn(), 'Sign out should clear the logged in state');
}

function testProfileAccess(): void
{
    reset_environment();
    $profile = XN_Profile::create('bob@example.com', 'password', ['fullName' => 'Bob Example']);
    $loaded = XN_Profile::load('bob');
    assertEquals($profile->email, $loaded->email, 'Profiles should be retrievable by screen name');
    assertEquals('Bob Example', $loaded->fullName, 'Custom attributes should persist');
}

function testContentQueryFlow(): void
{
    reset_environment();
    XN_Profile::create('carol@example.com', 'secret');
    XN_Profile::signIn('carol@example.com', 'secret');

    $content = XN_Content::create('BlogPost');
    $content->title = 'First Post';
    $content->description = 'Hello Ning!';
    $content->contributorName = XN_Profile::current()->screenName;
    $content->my->set('category', 'general');
    $content->save();

    $query = XN_Query::create('Content')
        ->filter('type', '=', 'BlogPost')
        ->filter('my->category', '=', 'general')
        ->order('createdDate', 'desc', XN_Attribute::DATE)
        ->begin(0)
        ->end(10);

    $results = $query->execute();
    assertEquals(1, count($results), 'Content query should return the saved record');
    assertEquals('First Post', $results[0]->title, 'Returned content should include the expected fields');
}

$tests = [
    'Authentication flow' => 'testAuthenticationFlow',
    'Profile access' => 'testProfileAccess',
    'Content query flow' => 'testContentQueryFlow',
];

$failures = 0;
foreach ($tests as $label => $callable) {
    try {
        $callable();
        echo "[PASS] {$label}\n";
    } catch (Throwable $e) {
        $failures++;
        echo "[FAIL] {$label}: " . $e->getMessage() . "\n";
    }
}

if ($failures > 0) {
    exit(1);
}

echo "All integration checks passed.\n";
