<?php
use Ning\SDK\Environment;
use Ning\SDK\Entities\Profile as ProfileEntity;
use Ning\SDK\Entities\Content as ContentEntity;
use Ning\SDK\Services\QueryService;
use Ning\SDK\Services\CacheService;
use Ning\SDK\Services\RestService;
use Ning\SDK\Query\QueryDefinition;
use Ning\SDK\Query\FilterNode;

if (!defined('XN_INCLUDE_PREFIX')) {
    define('XN_INCLUDE_PREFIX', realpath(__DIR__ . '/..'));
}
if (!defined('XN_PHP_START_TIME')) {
    define('XN_PHP_START_TIME', microtime(true));
}
if (!defined('XN_ALLOW_ADMIN_ONLY')) {
    define('XN_ALLOW_ADMIN_ONLY', false);
}
if (!defined('XN_SECURITY_TOKEN')) {
    define('XN_SECURITY_TOKEN', 'test-token');
}

class XN_Exception extends Exception {}
class XN_IllegalArgumentException extends InvalidArgumentException {}

class XN_Debug
{
    private static $allowed = false;

    public static function allowDebug($flag): void
    {
        self::$allowed = (bool)$flag;
    }

    public static function isAllowed(): bool
    {
        return self::$allowed;
    }
}

class XN_Application
{
    public static function load()
    {
        return Environment::application()->load();
    }
}

class XN_AtomHelper
{
    public static $DOMAIN_SUFFIX = '.example.com';

    public static function HOST_APP($subdomain)
    {
        return $subdomain . '.example.com';
    }

    public static function loadFromAtomFeed($response, $class)
    {
        return $response;
    }
}

if (!class_exists('XN_Atomhelper', false)) {
    class_alias('XN_AtomHelper', 'XN_Atomhelper');
}

class XN_Attribute
{
    const STRING = 'string';
    const NUMBER = 'number';
    const DATE = 'date';
}

class XN_Auth_Captcha
{
    public $token;

    public static function create($token = null)
    {
        $instance = new self();
        if ($token) {
            $instance->token = $token;
        } else {
            $instance->token = function_exists('random_bytes')
                ? bin2hex(random_bytes(4))
                : substr(sha1(uniqid('', true)), 0, 8);
        }
        return $instance;
    }
}

class XN_Profile extends ProfileEntity
{
    /** @var Ning\SDK\Services\ProfileService */
    private static $service;

    public static function setService($service): void
    {
        self::$service = $service;
    }

    public static function current(): self
    {
        return self::$service->current();
    }

    public static function create($email, $password, array $attributes = [])
    {
        return self::$service->create($email, $password, $attributes);
    }

    public static function signIn($email, $password, array $options = [])
    {
        return self::$service->signIn($email, $password, $options);
    }

    public static function signOut(): void
    {
        self::$service->signOut();
    }

    public static function load($identifier)
    {
        return self::$service->load($identifier);
    }

    public function save(): void
    {
        // Profiles are stored in-memory; nothing required here for compatibility.
    }

    public function isMember(): bool
    {
        return $this->isLoggedIn();
    }
}

class XN_Content extends ContentEntity
{
    /** @var Ning\SDK\Services\ContentService */
    private static $service;

    public static function setService($service): void
    {
        self::$service = $service;
    }

    public function __construct(string $type)
    {
        parent::__construct($type);
    }

    public static function create($type)
    {
        return new self($type);
    }

    public static function load($id)
    {
        return self::$service->load($id);
    }

    public static function delete($content): void
    {
        self::$service->delete($content);
    }

    public function save()
    {
        self::$service->save($this);
        return $this;
    }
}

class XN_Filter extends FilterNode
{
    public static function any(...$filters)
    {
        return FilterNode::group('any', self::normalize($filters));
    }

    public static function all(...$filters)
    {
        return FilterNode::group('all', self::normalize($filters));
    }

    public static function not($filter)
    {
        return FilterNode::group('not', [self::ensureFilter($filter)]);
    }

    public static function filter($field, $operator = null, $value = null, $type = null)
    {
        return FilterNode::simple($field, $operator, $value, $type);
    }

    private static function normalize(array $filters): array
    {
        if (count($filters) === 1 && is_array($filters[0])) {
            $filters = $filters[0];
        }
        return array_map([self::class, 'ensureFilter'], $filters);
    }

    private static function ensureFilter($filter): FilterNode
    {
        if ($filter instanceof FilterNode) {
            return $filter;
        }
        return self::filter($filter[0] ?? $filter, $filter[1] ?? null, $filter[2] ?? null, $filter[3] ?? null);
    }
}

function XN_Filter($field, $operator = null, $value = null, $type = null)
{
    return XN_Filter::filter($field, $operator, $value, $type);
}

class XN_Query extends QueryDefinition implements \IteratorAggregate
{
    /** @var QueryService */
    private static $service;

    public static function setService(QueryService $service): void
    {
        self::$service = $service;
    }

    public static function create($resourceType)
    {
        return new self($resourceType, self::$service);
    }

    public function __construct($resourceType, QueryService $service)
    {
        parent::__construct($resourceType, $service);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->execute());
    }

    public static function FRIENDS($screenName = null)
    {
        return [];
    }
}

class XN_Cache
{
    /** @var CacheService */
    private static $service;

    public static function setService(CacheService $service): void
    {
        self::$service = $service;
    }

    public static function get($key)
    {
        return self::$service->get($key);
    }

    public static function insert($key, $value)
    {
        return self::$service->insert($key, $value);
    }

    public static function put($key, $value)
    {
        return self::$service->put($key, $value);
    }

    public static function remove($key): void
    {
        self::$service->remove($key);
    }

    public static function invalidate($key): void
    {
        self::remove($key);
    }

    public static function content($id)
    {
        return XN_Content::load($id);
    }

    public static function profiles($screenName)
    {
        return XN_Profile::load($screenName);
    }
}

class XN_REST
{
    /** @var RestService */
    private static $service;

    public static function setService(RestService $service): void
    {
        self::$service = $service;
    }

    private static function request($method, $url, $params = null, $headers = null, $options = null)
    {
        return self::$service->request($method, $url, $params, $headers, $options);
    }

    public static function get($url, $params = null, $headers = null, $options = null)
    {
        return self::request('GET', $url, $params, $headers, $options);
    }

    public static function post($url, $params = null, $headers = null, $options = null)
    {
        return self::request('POST', $url, $params, $headers, $options);
    }

    public static function put($url, $params = null, $headers = null, $options = null)
    {
        return self::request('PUT', $url, $params, $headers, $options);
    }

    public static function delete($url, $params = null, $headers = null, $options = null)
    {
        return self::request('DELETE', $url, $params, $headers, $options);
    }

    public static function getLastResponseCode()
    {
        return 200;
    }
}

class XN_Request
{
    public static function uploadedFileContents($file)
    {
        if (is_array($file) && isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
            return file_get_contents($file['tmp_name']);
        }
        return null;
    }
}

class XN_ProfileSet
{
    const USERS = 'users';

    private static $sets = [];

    private $name;
    private $labels = [];
    private $members = [];

    private function __construct(string $name, array $labels = [])
    {
        $this->name = $name;
        $this->labels = $labels;
    }

    public static function load($name)
    {
        return self::$sets[$name] ?? null;
    }

    public static function loadOrCreate($name, array $labels = [])
    {
        if (!isset(self::$sets[$name])) {
            self::$sets[$name] = new self($name, $labels);
        } elseif ($labels) {
            self::$sets[$name]->labels = array_unique(array_merge(self::$sets[$name]->labels, $labels));
        }
        return self::$sets[$name];
    }

    public static function delete($name): void
    {
        unset(self::$sets[$name]);
    }

    public static function listSets(): array
    {
        return array_keys(self::$sets);
    }

    public static function addMembersToSets($members, ...$setNames): void
    {
        $members = (array)$members;
        if (count($setNames) === 1 && is_array($setNames[0])) {
            $setNames = $setNames[0];
        }
        foreach ($setNames as $setName) {
            $set = self::loadOrCreate($setName);
            $set->addMembers($members);
        }
    }

    public static function setContainsUser($set, $user): bool
    {
        $set = is_string($set) ? self::load($set) : $set;
        if (!$set) {
            return false;
        }
        $screenName = $user instanceof XN_Profile ? $user->screenName : $user;
        return in_array($screenName, $set->members, true);
    }

    public static function removeMemberByLabel($member, $label): bool
    {
        $set = self::load($label);
        if (!$set) {
            return false;
        }
        return $set->removeMember($member);
    }

    public function addMembers(array $members): void
    {
        foreach ($members as $member) {
            if (!in_array($member, $this->members, true)) {
                $this->members[] = $member;
            }
        }
    }

    public function removeMember($member): bool
    {
        $index = array_search($member, $this->members, true);
        if ($index === false) {
            return false;
        }
        array_splice($this->members, $index, 1);
        return true;
    }

    public function save(): void
    {
        // data lives in memory; nothing to persist.
    }
}

class XN_ProfileSetException extends XN_Exception {}

class XN_ProfileSetIterator implements \Iterator
{
    private $items;
    private $position = 0;

    public function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->items[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function next(): void
    {
        $this->position++;
    }

    #[\ReturnTypeWillChange]
    public function key(): int
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->position = 0;
    }
}

class XN_Message
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function create(array $data = [])
    {
        return new self($data);
    }

    public function send($recipient = null)
    {
        return true;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}

class XN_MessageFolder {}
class XN_Messages {}

class XN_Contact {}
class XN_ContactImportService
{
    public static function listServices(): array
    {
        return [];
    }
}
class XN_ContactImportResult
{
    public $status = 'COMPLETE';
    const COMPLETE = 'COMPLETE';

    public static function load($id)
    {
        return new self();
    }
}
class XN_ContactImportServices {}
class XN_ImportedContact {}

class XN_Invitation {}
class XN_Invitations {}

class XN_Job {}
class XN_Jobs {}

class XN_Task
{
    public static function create($url, array $parameters = [])
    {
        return ['url' => $url, 'parameters' => $parameters];
    }
}
class XN_Tasks
{
    public static function create($name)
    {
        return ['name' => $name];
    }
}

class XN_SearchResult {}
class XN_SearchResults {}

class XN_Shape
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function save(): void
    {
        // noop
    }
}

class XN_Tag
{
    public static function create($tags)
    {
        return (array)$tags;
    }
}

class XN_Message_Notification
{
    private $event;
    private $data;

    public function __construct($event, array $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    public static function create($event, array $data)
    {
        return new self($event, $data);
    }

    public function send($recipient)
    {
        return true;
    }
}

