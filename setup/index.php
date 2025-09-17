<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$baseDir = dirname(__DIR__);
$configPath = $baseDir . '/config/app.php';
$sessionKey = 'nf_setup_wizard';
$completed = !empty($_SESSION[$sessionKey]['completed']);

if (is_file($configPath) && !$completed) {
    $redirectUrl = '../';
    if (PHP_SAPI !== 'cli' && !headers_sent()) {
        header('Location: ' . $redirectUrl);
    } else {
        echo "Configuration already exists. Visit {$redirectUrl} to launch the application." . PHP_EOL;
    }
    exit;
}

$wizard = new SetupWizard($baseDir, $configPath);
$wizard->handle();

class SetupWizard
{
    public const SESSION_KEY = 'nf_setup_wizard';

    private string $baseDir;
    private string $configPath;

    /**
     * @var array<string,mixed>
     */
    private array $state;

    /**
     * TLD suffixes that represent controlled registries where the registrable domain
     * includes an additional label (for example, "example.co.uk").
     *
     * @var list<string>
     */
    private const MULTI_LEVEL_TLDS = [
        'com.au',
        'net.au',
        'org.au',
        'edu.au',
        'gov.au',
        'asn.au',
        'id.au',
        'com.br',
        'com.cn',
        'co.jp',
        'or.jp',
        'ne.jp',
        'ac.jp',
        'go.jp',
        'co.nz',
        'org.nz',
        'govt.nz',
        'ac.nz',
        'co.uk',
        'org.uk',
        'gov.uk',
        'ac.uk',
        'net.uk',
        'sch.uk',
    ];

    public function __construct(string $baseDir, string $configPath)
    {
        $this->baseDir = $baseDir;
        $this->configPath = $configPath;

        $detected = $this->detectEnvironment();
        $defaultState = $this->buildDefaultState($detected);

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = $defaultState;
        } else {
            $_SESSION[self::SESSION_KEY] = array_replace_recursive(
                $defaultState,
                $_SESSION[self::SESSION_KEY]
            );
        }

        $this->state = &$_SESSION[self::SESSION_KEY];
        $this->state['environment']['detected'] = $detected;
    }

    /**
     * @param array<string,mixed> $detected
     * @return array<string,mixed>
     */
    private function buildDefaultState(array $detected): array
    {
        $networkDefaults = $this->defaultNetworkConfiguration($detected);
        $baseUrl = $networkDefaults['suggested_base_url'];
        $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);
        if ($normalizedBaseUrl !== null) {
            $baseUrl = $normalizedBaseUrl;
        }

        return [
            'environment' => [
                'site_name' => 'My Network',
                'base_url' => $baseUrl,
                'force_https' => !empty($detected['https_detected']),
                'detected' => $detected,
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'name' => '',
                'user' => '',
                'password' => '',
            ],
            'super_admin' => [
                'email' => '',
                'password_hash' => '',
            ],
            'network_admin' => [
                'email' => '',
                'password_hash' => '',
            ],
            'network' => [
                'name' => 'My Network',
                'slug' => $networkDefaults['default_slug'],
                'base_domain' => $networkDefaults['base_domain'],
                'base_path' => $networkDefaults['default_base_path'],
                'use_subdomain' => $networkDefaults['use_subdomain'],
                'aliases' => [],
                'auto_updates' => true,
                'allow_super_admin_updates' => true,
            ],
            'automation' => [
                'auto_updates' => true,
                'allow_network_override' => true,
                'updates_branch' => 'main',
                'scheduler_mode' => 'system_cron',
                'scheduler_interval' => 15,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $detected
     * @return array{base_domain:string,use_subdomain:bool,default_slug:string,default_base_path:string,suggested_base_url:string}
     */
    private function defaultNetworkConfiguration(array $detected): array
    {
        $host = strtolower((string) ($detected['host'] ?? 'localhost'));
        if ($host === '') {
            $host = 'localhost';
        }

        $https = !empty($detected['https_detected']);
        $scheme = $https ? 'https' : 'http';
        $port = isset($detected['port']) ? (int) $detected['port'] : null;

        $baseDomain = $this->deriveBaseDomain($host);
        $useSubdomain = !$this->isIpAddress($host) && $host !== $baseDomain;
        $defaultSlug = $useSubdomain ? $this->deriveSlugFromHost($host, $baseDomain) : 'network';
        if ($defaultSlug === '') {
            $defaultSlug = 'network';
        }

        $baseUrl = (string) ($detected['base_url'] ?? '');
        if ($baseUrl === '') {
            $baseUrl = $scheme . '://' . $host;
            if ($port !== null && !$this->isDefaultPort($https, $port)) {
                $baseUrl .= ':' . $port;
            }
        }
        $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);
        $baseUrlForPath = $normalizedBaseUrl ?? $baseUrl;

        $defaultBasePath = '/';
        if (!$useSubdomain) {
            $parsedPath = parse_url($baseUrlForPath, PHP_URL_PATH);
            if (is_string($parsedPath) && $parsedPath !== '') {
                $normalizedPath = $this->normalizeBasePath($parsedPath);
                if ($normalizedPath !== null) {
                    $defaultBasePath = $normalizedPath;
                }
            }
            if ($defaultBasePath === '/' && isset($detected['base_path'])) {
                $fallbackPath = $this->normalizeBasePath((string) $detected['base_path']);
                if ($fallbackPath !== null) {
                    $defaultBasePath = $fallbackPath;
                }
            }
        }

        return [
            'base_domain' => $baseDomain,
            'use_subdomain' => $useSubdomain,
            'default_slug' => $defaultSlug,
            'default_base_path' => $defaultBasePath,
            'suggested_base_url' => $normalizedBaseUrl ?? $baseUrl,
        ];
    }

    public function handle(): void
    {
        $step = $this->getRequestedStep();
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            $errors = $this->processStep($step);
            if (empty($errors)) {
                $next = $this->getNextStep($step);
                if ($next === 'complete') {
                    $this->redirectToStep('complete');
                    return;
                }

                if ($next !== $step) {
                    $this->redirectToStep((string) $next);
                    return;
                }
            }
        }

        $this->render($step, $errors ?? []);
    }

    /**
     * @return string|int
     */
    private function getRequestedStep()
    {
        $step = $_GET['step'] ?? '1';
        if ($step === 'complete') {
            return 'complete';
        }

        $step = (int) $step;
        if ($step < 1 || $step > 5) {
            $step = 1;
        }

        return $step;
    }

    /**
     * @param int|string $currentStep
     * @return int|string
     */
    private function getNextStep($currentStep)
    {
        if ($currentStep === 'complete') {
            return 'complete';
        }

        if ($currentStep >= 5) {
            return 'complete';
        }

        return $currentStep + 1;
    }

    private function redirectToStep(string $step): void
    {
        $location = '?step=' . urlencode($step);
        if (PHP_SAPI !== 'cli' && !headers_sent()) {
            header('Location: ' . $location);
        } else {
            echo 'Continue setup at: ' . $location . PHP_EOL;
        }
        exit;
    }

    /**
     * @return array<string,string>
     */
    private function processStep($step): array
    {
        if ($step === 'complete') {
            return [];
        }

        switch ((int) $step) {
            case 1:
                return $this->handleEnvironmentStep();
            case 2:
                return $this->handleDatabaseStep();
            case 3:
                return $this->handleAdministratorsStep();
            case 4:
                return $this->handleNetworkAndAutomationStep();
            case 5:
                return $this->handleFinalizeStep();
            default:
                return [];
        }
    }

    /**
     * @return array<string,string>
     */
    private function handleEnvironmentStep(): array
    {
        $siteName = trim($_POST['site_name'] ?? '');
        $baseUrl = trim($_POST['base_url'] ?? '');
        $forceHttps = isset($_POST['force_https']) && $_POST['force_https'] === '1';

        $errors = [];
        $normalizedBaseUrl = null;

        if ($siteName === '') {
            $errors['site_name'] = 'Please enter a name for your network.';
        }

        if ($baseUrl === '' || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $errors['base_url'] = 'Please provide a valid base URL (e.g. https://example.com).';
        } else {
            $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);
            if ($normalizedBaseUrl === null) {
                $errors['base_url'] = 'Please provide a valid base URL (e.g. https://example.com).';
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        $this->state['environment']['site_name'] = $siteName;
        $this->state['environment']['base_url'] = $normalizedBaseUrl ?? $baseUrl;
        $this->state['environment']['force_https'] = $forceHttps;
        $this->state['environment']['detected'] = $this->detectEnvironment();

        return [];
    }

    /**
     * @return array<string,string>
     */
    private function handleDatabaseStep(): array
    {
        $host = trim($_POST['db_host'] ?? '');
        $port = trim($_POST['db_port'] ?? '3306');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? '');
        $password = (string) ($_POST['db_password'] ?? '');

        $errors = [];

        if ($host === '') {
            $errors['db_host'] = 'Database host is required.';
        }

        if (!preg_match('/^\d+$/', $port) || (int) $port < 1 || (int) $port > 65535) {
            $errors['db_port'] = 'Database port must be a number between 1 and 65535.';
        }

        if ($name === '') {
            $errors['db_name'] = 'Database name is required.';
        }

        if ($user === '') {
            $errors['db_user'] = 'Database username is required.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, (int) $port);

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $quotedName = '`' . str_replace('`', '``', $name) . '`';
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$quotedName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $exception) {
            $errors['connection'] = 'Unable to connect to the database using the provided credentials: ' . $exception->getMessage();
            return $errors;
        }

        $this->state['database'] = [
            'host' => $host,
            'port' => (int) $port,
            'name' => $name,
            'user' => $user,
            'password' => $password,
        ];

        return [];
    }
    /**
     * @return array<string,string>
     */
    private function handleAdministratorsStep(): array
    {
        $superEmail = trim($_POST['super_admin_email'] ?? '');
        $superPassword = (string) ($_POST['super_admin_password'] ?? '');
        $superConfirm = (string) ($_POST['super_admin_password_confirm'] ?? '');

        $networkEmail = trim($_POST['network_admin_email'] ?? '');
        $networkPassword = (string) ($_POST['network_admin_password'] ?? '');
        $networkConfirm = (string) ($_POST['network_admin_password_confirm'] ?? '');

        $errors = [];

        if (!filter_var($superEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['super_admin_email'] = 'Please provide a valid email address for the super administrator.';
        }

        if (strlen($superPassword) < 8) {
            $errors['super_admin_password'] = 'The super administrator password must be at least 8 characters long.';
        }

        if (!hash_equals($superPassword, $superConfirm)) {
            $errors['super_admin_password_confirm'] = 'The super administrator password confirmation does not match.';
        }

        if (!filter_var($networkEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['network_admin_email'] = 'Please provide a valid email address for the network administrator.';
        }

        if (strlen($networkPassword) < 8) {
            $errors['network_admin_password'] = 'The network administrator password must be at least 8 characters long.';
        }

        if (!hash_equals($networkPassword, $networkConfirm)) {
            $errors['network_admin_password_confirm'] = 'The network administrator password confirmation does not match.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $this->state['super_admin'] = [
            'email' => $superEmail,
            'password_hash' => password_hash($superPassword, PASSWORD_DEFAULT),
        ];

        $this->state['network_admin'] = [
            'email' => $networkEmail,
            'password_hash' => password_hash($networkPassword, PASSWORD_DEFAULT),
        ];

        return [];
    }

    /**
     * @return array<string,string>
     */
    private function handleNetworkAndAutomationStep(): array
    {
        $networkName = trim($_POST['network_name'] ?? '');
        $networkSlug = strtolower(trim($_POST['network_slug'] ?? ''));
        $baseDomain = strtolower(trim($_POST['network_base_domain'] ?? ''));
        $useSubdomain = isset($_POST['network_use_subdomain']) && $_POST['network_use_subdomain'] === '1';
        $basePath = trim($_POST['network_base_path'] ?? '/');
        $aliasesRaw = trim((string) ($_POST['network_aliases'] ?? ''));
        $networkAutoUpdates = isset($_POST['network_auto_updates']) && $_POST['network_auto_updates'] === '1';
        $allowSuperAdminUpdates = isset($_POST['network_allow_super_updates']) && $_POST['network_allow_super_updates'] === '1';

        $globalAutoUpdates = isset($_POST['auto_updates']) && $_POST['auto_updates'] === '1';
        $allowNetworkOverride = isset($_POST['auto_updates_allow_override']) && $_POST['auto_updates_allow_override'] === '1';
        $updatesBranch = trim($_POST['auto_updates_branch'] ?? 'main');
        $schedulerMode = $_POST['scheduler_mode'] ?? 'system_cron';
        $schedulerInterval = (int) ($_POST['scheduler_interval'] ?? 15);

        $errors = [];

        if ($networkName === '') {
            $errors['network_name'] = 'Please choose a name for this network.';
        }

        if ($networkSlug === '' || !preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $networkSlug)) {
            $errors['network_slug'] = 'Provide a valid subdomain slug using letters, numbers, and dashes.';
        }

        if ($baseDomain === '') {
            $errors['network_base_domain'] = 'A base domain is required.';
        } elseif (!$this->isValidHost($baseDomain)) {
            $errors['network_base_domain'] = 'The base domain contains invalid characters.';
        }

        $normalizedBasePath = $this->normalizeBasePath($basePath);
        if ($normalizedBasePath === null) {
            $errors['network_base_path'] = 'The base path may only contain letters, numbers, dashes, underscores, and forward slashes.';
        }

        $aliases = $this->normalizeAliases($aliasesRaw);

        $this->state['network'] = [
            'name' => $networkName,
            'slug' => $networkSlug,
            'base_domain' => $baseDomain,
            'base_path' => $basePath,
            'use_subdomain' => $useSubdomain,
            'aliases' => $aliases,
            'auto_updates' => $networkAutoUpdates,
            'allow_super_admin_updates' => $allowSuperAdminUpdates,
        ];

        $this->state['automation'] = [
            'auto_updates' => $globalAutoUpdates,
            'allow_network_override' => $allowNetworkOverride,
            'updates_branch' => $updatesBranch,
            'scheduler_mode' => $schedulerMode,
            'scheduler_interval' => $schedulerInterval,
        ];

        $invalidAliases = [];
        foreach ($aliases as $alias) {
            if (!$this->isValidHost($alias)) {
                $invalidAliases[] = $alias;
            }
        }

        if (!empty($invalidAliases)) {
            $errors['network_aliases'] = 'These aliases are not valid hostnames: ' . implode(', ', $invalidAliases) . '.';
        }

        $primaryHost = $useSubdomain ? $networkSlug . '.' . $baseDomain : $baseDomain;
        if (in_array($primaryHost, $aliases, true)) {
            $errors['network_aliases'] = 'Alias domains may not duplicate the primary host.';
        }

        $duplicateAliases = [];
        if (!empty($aliases)) {
            $aliasCounts = array_count_values($aliases);
            foreach ($aliasCounts as $alias => $count) {
                if ($count > 1) {
                    $duplicateAliases[] = $alias;
                }
            }
        }

        if (!empty($duplicateAliases)) {
            $message = 'Alias domains must be unique. Remove duplicates: ' . implode(', ', $duplicateAliases) . '.';
            if (isset($errors['network_aliases'])) {
                $errors['network_aliases'] .= ' ' . $message;
            } else {
                $errors['network_aliases'] = $message;
            }
        }

        if ($updatesBranch === '') {
            $errors['auto_updates_branch'] = 'Provide the Git branch or tag to track for automatic updates.';
        } elseif (!preg_match('~^[A-Za-z0-9._\-\/]+$~', $updatesBranch)) {
            $errors['auto_updates_branch'] = 'The updates branch may only contain letters, numbers, dots, dashes, slashes, and underscores.';
        }

        if (!in_array($schedulerMode, ['system_cron', 'page_load'], true)) {
            $errors['scheduler_mode'] = 'Choose a valid scheduler mode.';
        }

        if ($schedulerInterval < 1 || $schedulerInterval > 1440) {
            $errors['scheduler_interval'] = 'Scheduler interval must be between 1 and 1440 minutes.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $uniqueAliases = array_values(array_unique($aliases));

        $this->state['network'] = [
            'name' => $networkName,
            'slug' => $networkSlug,
            'base_domain' => $baseDomain,
            'base_path' => $normalizedBasePath ?? '/',
            'use_subdomain' => $useSubdomain,
            'aliases' => $uniqueAliases,
            'auto_updates' => $networkAutoUpdates,
            'allow_super_admin_updates' => $allowSuperAdminUpdates,
        ];

        $this->state['automation'] = [
            'auto_updates' => $globalAutoUpdates,
            'allow_network_override' => $allowNetworkOverride,
            'updates_branch' => $updatesBranch,
            'scheduler_mode' => $schedulerMode,
            'scheduler_interval' => $schedulerInterval,
        ];

        return [];
    }

    /**
     * @return array<string,string>
     */
    private function handleFinalizeStep(): array
    {
        if (empty($this->state['database']['host']) || empty($this->state['database']['name']) || empty($this->state['database']['user'])) {
            return ['config' => 'Database configuration is incomplete. Please return to the previous steps and review your entries.'];
        }

        if (empty($this->state['super_admin']['email']) || empty($this->state['super_admin']['password_hash'])) {
            return ['config' => 'Super administrator details are missing. Please provide the email and password.'];
        }

        if (empty($this->state['network_admin']['email']) || empty($this->state['network_admin']['password_hash'])) {
            return ['config' => 'Network administrator details are missing. Please provide the email and password.'];
        }

        if (empty($this->state['network']['name']) || empty($this->state['network']['slug']) || empty($this->state['network']['base_domain'])) {
            return ['config' => 'Network settings are incomplete. Please confirm the network domain and subdomain selections.'];
        }

        $config = $this->buildConfiguration();
        $configDir = dirname($this->configPath);

        if (!is_dir($configDir) && !mkdir($configDir, 0755, true) && !is_dir($configDir)) {
            return ['config' => 'Unable to create configuration directory: ' . $configDir];
        }

        if (is_file($this->configPath)) {
            if (!is_writable($this->configPath)) {
                return ['config' => 'The existing configuration file is not writable: ' . $this->configPath];
            }
        } elseif (!is_writable($configDir)) {
            return ['config' => 'The configuration directory is not writable: ' . $configDir];
        }

        $configContents = "<?php\nreturn " . var_export($config, true) . ";\n";

        if (@file_put_contents($this->configPath, $configContents) === false) {
            return ['config' => 'Unable to write configuration file: ' . $this->configPath];
        }

        @chmod($this->configPath, 0640);

        $this->state['completed'] = true;
        unset($this->state['database']);
        if (isset($this->state['super_admin']['password_hash'])) {
            $this->state['super_admin']['password_hash'] = '[stored]';
        }
        if (isset($this->state['network_admin']['password_hash'])) {
            $this->state['network_admin']['password_hash'] = '[stored]';
        }

        return [];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildConfiguration(): array
    {
        $environment = $this->state['environment'] ?? [];
        $database = $this->state['database'] ?? [];
        $superAdmin = $this->state['super_admin'] ?? [];
        $networkAdmin = $this->state['network_admin'] ?? [];
        $network = $this->state['network'] ?? [];
        $automation = $this->state['automation'] ?? [];

        $scheme = 'http';
        if (!empty($environment['force_https'])) {
            $scheme = 'https';
        } elseif (!empty($environment['base_url'])) {
            $parsedScheme = parse_url((string) $environment['base_url'], PHP_URL_SCHEME);
            if (is_string($parsedScheme)) {
                $scheme = strtolower($parsedScheme);
            }
        }

        $port = null;
        if (!empty($environment['base_url'])) {
            $parsedPort = parse_url((string) $environment['base_url'], PHP_URL_PORT);
            if (is_int($parsedPort)) {
                $port = $parsedPort;
            }
        }

        if ($port === null && isset($environment['detected']['port'])) {
            $detectedPort = (int) $environment['detected']['port'];
            if ($detectedPort > 0) {
                $port = $detectedPort;
            }
        }

        $useSubdomain = !empty($network['use_subdomain']);
        $primaryHost = $useSubdomain
            ? ($network['slug'] ?? 'network') . '.' . ($network['base_domain'] ?? 'localhost')
            : ($network['base_domain'] ?? 'localhost');

        $primaryUrl = $scheme . '://' . $primaryHost;
        if ($port !== null && !$this->isDefaultPort($scheme === 'https', (int) $port)) {
            $primaryUrl .= ':' . $port;
        }

        $basePath = $useSubdomain ? '/' : ($network['base_path'] ?? '/');
        if ($basePath !== '/' && $basePath !== '') {
            $primaryUrl .= $basePath;
        }

        $aliases = $network['aliases'] ?? [];

        return [
            'app' => [
                'name' => $environment['site_name'] ?? 'My Network',
                'base_url' => $environment['base_url'] ?? '',
                'force_https' => (bool) ($environment['force_https'] ?? false),
                'detected' => $this->detectEnvironment(),
                'created_at' => date(DATE_ATOM),
            ],
            'database' => [
                'host' => $database['host'] ?? 'localhost',
                'port' => $database['port'] ?? 3306,
                'name' => $database['name'] ?? '',
                'user' => $database['user'] ?? '',
                'password' => $database['password'] ?? '',
                'dsn' => sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $database['host'] ?? 'localhost',
                    (int) ($database['port'] ?? 3306),
                    $database['name'] ?? ''
                ),
            ],
            'super_admin' => $superAdmin,
            'networks' => [
                $network['slug'] ?? 'network' => [
                    'id' => $network['slug'] ?? 'network',
                    'name' => $network['name'] ?? 'My Network',
                    'primary_host' => $primaryHost,
                    'primary_url' => $primaryUrl,
                    'base_domain' => $network['base_domain'] ?? 'localhost',
                    'use_subdomain' => (bool) $useSubdomain,
                    'base_path' => $basePath,
                    'aliases' => $aliases,
                    'auto_updates' => [
                        'enabled' => (bool) ($network['auto_updates'] ?? true),
                        'allow_super_admin_deployments' => (bool) ($network['allow_super_admin_updates'] ?? true),
                    ],
                    'admins' => [
                        [
                            'email' => $networkAdmin['email'] ?? '',
                            'password_hash' => $networkAdmin['password_hash'] ?? '',
                            'role' => 'owner',
                        ],
                    ],
                ],
            ],
            'updates' => [
                'auto_deploy' => (bool) ($automation['auto_updates'] ?? true),
                'provider' => 'github',
                'branch' => $automation['updates_branch'] ?? 'main',
                'allow_network_opt_out' => (bool) ($automation['allow_network_override'] ?? true),
            ],
            'scheduler' => [
                'mode' => $automation['scheduler_mode'] ?? 'system_cron',
                'interval_minutes' => (int) ($automation['scheduler_interval'] ?? 15),
                'last_run' => null,
            ],
        ];
    }
    /**
     * @return array<string,mixed>
     */
    private function detectEnvironment(): array
    {
        $forwardedProto = $this->firstHeaderValue((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $forwardedHost = $this->firstHeaderValue((string) ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? ''));
        $forwardedPort = $this->firstHeaderValue((string) ($_SERVER['HTTP_X_FORWARDED_PORT'] ?? ''));

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
            || (($_SERVER['REQUEST_SCHEME'] ?? '') === 'https')
            || ($forwardedProto !== '' && strtolower($forwardedProto) === 'https');

        $hostHeader = $forwardedHost !== ''
            ? $forwardedHost
            : ($_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost'));

        [$host, $portFromHeader] = $this->extractHostAndPort((string) $hostHeader);
        $host = $this->sanitizeDetectedHost($host);

        $port = null;
        if ($forwardedPort !== '' && ctype_digit($forwardedPort)) {
            $port = (int) $forwardedPort;
        } elseif ($portFromHeader !== null) {
            $port = $portFromHeader;
        } elseif (isset($_SERVER['SERVER_PORT']) && ctype_digit((string) $_SERVER['SERVER_PORT'])) {
            $port = (int) $_SERVER['SERVER_PORT'];
        }

        if ($port === null || $port <= 0) {
            $port = $https ? 443 : 80;
        }

        $scheme = $https ? 'https' : 'http';
        $baseUrl = $scheme . '://' . $this->formatHostForUrl($host);
        if (!$this->isDefaultPort($https, $port)) {
            $baseUrl .= ':' . $port;
        }

        $basePath = $this->detectBasePath();
        if ($basePath !== '/' && $basePath !== '') {
            $baseUrl .= $basePath;
        }

        $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);

        return [
            'php_version' => PHP_VERSION,
            'extensions' => get_loaded_extensions(),
            'https_detected' => $https,
            'host' => $host,
            'port' => $port,
            'base_url' => $normalizedBaseUrl ?? $baseUrl,
            'base_path' => $basePath,
        ];
    }

    /**
     * @param int|string $step
     * @param array<string,string> $errors
     */
    private function render($step, array $errors): void
    {
        if (PHP_SAPI !== 'cli' && !headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        $title = 'Setup Wizard';

        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
        echo '<style>' . $this->getStyles() . '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="wizard">';
        echo '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';

        if ($step === 'complete') {
            $this->renderCompletion();
        } else {
            $this->renderProgress((int) $step);
            $this->renderStep((int) $step, $errors);
        }

        echo '</div>';
        echo '</body>';
        echo '</html>';
    }

    private function renderCompletion(): void
    {
        if (!empty($this->state['completed'])) {
            echo '<p>Your platform is configured with multi-network management enabled.</p>';
            echo '<p><a class="button" href="../">Launch the Control Panel</a></p>';
        } else {
            echo '<p>The setup wizard has already been completed.</p>';
            echo '<p><a class="button" href="../">Return to the site</a></p>';
        }
    }

    private function renderProgress(int $step): void
    {
        $steps = [
            1 => 'Environment',
            2 => 'Database',
            3 => 'Administrators',
            4 => 'Network & Automation',
            5 => 'Finalize',
        ];

        echo '<ol class="progress">';
        foreach ($steps as $index => $label) {
            $class = $index === $step ? 'current' : ($index < $step ? 'complete' : '');
            echo '<li class="' . $class . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        echo '</ol>';
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderStep(int $step, array $errors): void
    {
        switch ($step) {
            case 1:
                $this->renderEnvironmentStep($errors);
                break;
            case 2:
                $this->renderDatabaseStep($errors);
                break;
            case 3:
                $this->renderAdministratorsStep($errors);
                break;
            case 4:
                $this->renderNetworkStep($errors);
                break;
            case 5:
                $this->renderFinalizeStep($errors);
                break;
        }
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderEnvironmentStep(array $errors): void
    {
        $environment = $this->state['environment'] ?? [];
        $detected = $environment['detected'] ?? $this->detectEnvironment();
        $siteName = $environment['site_name'] ?? 'My Network';
        $baseUrl = $environment['base_url'] ?? ($detected['base_url'] ?? '');
        $forceHttps = array_key_exists('force_https', $environment) ? (bool) $environment['force_https'] : (!empty($detected['https_detected']));
        $networkDefaults = $this->defaultNetworkConfiguration($detected);

        echo '<form method="post">';
        echo '<h2>Environment</h2>';
        echo '<p>Confirm the detected environment details. You can adjust them before provisioning networks.</p>';

        echo $this->renderField('Platform Name', 'site_name', $siteName, $errors['site_name'] ?? null);
        echo $this->renderField('Base URL', 'base_url', $baseUrl, $errors['base_url'] ?? null, 'url');

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="force_https" value="1"' . ($forceHttps ? ' checked' : '') . '>';
        echo ' Always enforce HTTPS for all requests';
        echo '</label>';

        echo '<div class="detected">';
        echo '<h3>Detected Settings</h3>';
        echo '<ul>';
        echo '<li>PHP Version: ' . htmlspecialchars($detected['php_version'] ?? PHP_VERSION, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>HTTPS: ' . (!empty($detected['https_detected']) ? 'Yes' : 'No') . '</li>';
        echo '<li>Host: ' . htmlspecialchars((string) ($detected['host'] ?? 'localhost'), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Port: ' . htmlspecialchars((string) ($detected['port'] ?? 80), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Suggested Base Domain: ' . htmlspecialchars($networkDefaults['base_domain'], ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Suggested Subdomain: ' . htmlspecialchars($networkDefaults['default_slug'], ENT_QUOTES, 'UTF-8') . '</li>';
        echo '</ul>';

        $requirements = $this->getRequirementWarnings();
        if (!empty($requirements)) {
            echo '<div class="requirements">';
            echo '<h4>Requirements Check</h4>';
            echo '<ul>';
            foreach ($requirements as $requirement) {
                echo '<li>' . htmlspecialchars($requirement, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        echo '</div>';

        echo '<div class="actions">';
        echo '<button type="submit">Continue to Database</button>';
        echo '</div>';
        echo '</form>';
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderDatabaseStep(array $errors): void
    {
        $database = $this->state['database'] ?? [
            'host' => 'localhost',
            'port' => 3306,
            'name' => '',
            'user' => '',
            'password' => '',
        ];

        echo '<form method="post">';
        echo '<h2>Database Connection</h2>';
        echo '<p>Enter your MySQL connection details. The wizard will verify the connection and create the database if it does not exist.</p>';

        echo $this->renderField('Host', 'db_host', (string) $database['host'], $errors['db_host'] ?? null);
        echo $this->renderField('Port', 'db_port', (string) $database['port'], $errors['db_port'] ?? null, 'number');
        echo $this->renderField('Database Name', 'db_name', (string) $database['name'], $errors['db_name'] ?? null);
        echo $this->renderField('Username', 'db_user', (string) $database['user'], $errors['db_user'] ?? null);

        echo '<label>';
        echo '<span>Password</span>';
        echo '<input type="password" name="db_password" value="' . htmlspecialchars((string) $database['password'], ENT_QUOTES, 'UTF-8') . '">';
        if (isset($errors['db_password'])) {
            echo '<small class="error">' . htmlspecialchars($errors['db_password'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        if (isset($errors['connection'])) {
            echo '<p class="error">' . htmlspecialchars($errors['connection'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=1">Back</a>';
        echo '<button type="submit">Continue to Administrators</button>';
        echo '</div>';
        echo '</form>';
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderAdministratorsStep(array $errors): void
    {
        $superAdmin = $this->state['super_admin'] ?? [
            'email' => '',
        ];
        $networkAdmin = $this->state['network_admin'] ?? [
            'email' => '',
        ];

        echo '<form method="post">';
        echo '<h2>Administrators</h2>';
        echo '<p>Define the global super administrator and the initial network administrator.</p>';

        echo '<div class="section">';
        echo '<h3>Super Administrator</h3>';
        echo '<p class="help">The super administrator manages every network, feature, and deployment.</p>';
        echo $this->renderField('Email Address', 'super_admin_email', (string) ($superAdmin['email'] ?? ''), $errors['super_admin_email'] ?? null, 'email');

        echo '<label>';
        echo '<span>Password</span>';
        echo '<input type="password" name="super_admin_password" value="">';
        if (isset($errors['super_admin_password'])) {
            echo '<small class="error">' . htmlspecialchars($errors['super_admin_password'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        echo '<label>';
        echo '<span>Confirm Password</span>';
        echo '<input type="password" name="super_admin_password_confirm" value="">';
        if (isset($errors['super_admin_password_confirm'])) {
            echo '<small class="error">' . htmlspecialchars($errors['super_admin_password_confirm'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h3>Network Administrator</h3>';
        echo '<p class="help">Each network can appoint its own administrators. The first one created here will own the default network.</p>';
        echo $this->renderField('Email Address', 'network_admin_email', (string) ($networkAdmin['email'] ?? ''), $errors['network_admin_email'] ?? null, 'email');

        echo '<label>';
        echo '<span>Password</span>';
        echo '<input type="password" name="network_admin_password" value="">';
        if (isset($errors['network_admin_password'])) {
            echo '<small class="error">' . htmlspecialchars($errors['network_admin_password'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        echo '<label>';
        echo '<span>Confirm Password</span>';
        echo '<input type="password" name="network_admin_password_confirm" value="">';
        if (isset($errors['network_admin_password_confirm'])) {
            echo '<small class="error">' . htmlspecialchars($errors['network_admin_password_confirm'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=2">Back</a>';
        echo '<button type="submit">Continue to Network</button>';
        echo '</div>';
        echo '</form>';
    }
    /**
     * @param array<string,string> $errors
     */
    private function renderNetworkStep(array $errors): void
    {
        $network = $this->state['network'] ?? [
            'name' => 'My Network',
            'slug' => 'network',
            'base_domain' => 'localhost',
            'base_path' => '/',
            'use_subdomain' => true,
            'aliases' => [],
            'auto_updates' => true,
            'allow_super_admin_updates' => true,
        ];
        $automation = $this->state['automation'] ?? [
            'auto_updates' => true,
            'allow_network_override' => true,
            'updates_branch' => 'main',
            'scheduler_mode' => 'system_cron',
            'scheduler_interval' => 15,
        ];

        $aliasesValue = implode("\n", $network['aliases'] ?? []);
        $useSubdomain = !empty($network['use_subdomain']);

        echo '<form method="post">';
        echo '<h2>Network & Automation</h2>';
        echo '<p>Configure how this network is addressed and how platform-wide automation operates.</p>';

        echo '<div class="section">';
        echo '<h3>Network Addressing</h3>';
        echo $this->renderField('Network Name', 'network_name', (string) ($network['name'] ?? ''), $errors['network_name'] ?? null);
        echo $this->renderField('Preferred Subdomain', 'network_slug', (string) ($network['slug'] ?? ''), $errors['network_slug'] ?? null);
        echo $this->renderField('Base Domain', 'network_base_domain', (string) ($network['base_domain'] ?? ''), $errors['network_base_domain'] ?? null);
        echo $this->renderField('Base Path (if not using subdomains)', 'network_base_path', (string) ($network['base_path'] ?? '/'), $errors['network_base_path'] ?? null);

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="network_use_subdomain" value="1"' . ($useSubdomain ? ' checked' : '') . '>';
        echo ' Route the network through the subdomain ' . htmlspecialchars((string) ($network['slug'] ?? ''), ENT_QUOTES, 'UTF-8') . '.';
        echo '</label>';

        echo $this->renderTextareaField('Alias Domains', 'network_aliases', $aliasesValue, $errors['network_aliases'] ?? null, false, [
            'rows' => 3,
            'placeholder' => "community.example.org\nexample.net",
        ]);
        echo '<p class="help">Provide optional hostnames that should point to this network. Separate entries with commas or new lines.</p>';

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="network_auto_updates" value="1"' . (!empty($network['auto_updates']) ? ' checked' : '') . '>';
        echo ' Enable automatic updates for this network by default';
        echo '</label>';

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="network_allow_super_updates" value="1"' . (!empty($network['allow_super_admin_updates']) ? ' checked' : '') . '>';
        echo ' Allow the super administrator to deploy updates to this network';
        echo '</label>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h3>Automatic Updates & Scheduling</h3>';
        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="auto_updates" value="1"' . (!empty($automation['auto_updates']) ? ' checked' : '') . '>';
        echo ' Automatically fetch platform updates from GitHub';
        echo '</label>';

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="auto_updates_allow_override" value="1"' . (!empty($automation['allow_network_override']) ? ' checked' : '') . '>';
        echo ' Allow network administrators to opt out of automatic deployments';
        echo '</label>';

        echo $this->renderField('Updates Branch or Tag', 'auto_updates_branch', (string) ($automation['updates_branch'] ?? 'main'), $errors['auto_updates_branch'] ?? null);

        echo '<label>';
        echo '<span>Scheduler Mode</span>';
        echo '<select name="scheduler_mode">';
        $mode = $automation['scheduler_mode'] ?? 'system_cron';
        $options = [
            'system_cron' => 'System Cron (recommended)',
            'page_load' => 'Page Load Trigger',
        ];
        foreach ($options as $value => $label) {
            $selected = $mode === $value ? ' selected' : '';
            echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';
        if (isset($errors['scheduler_mode'])) {
            echo '<small class="error">' . htmlspecialchars($errors['scheduler_mode'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        echo $this->renderField('Scheduler Interval (minutes)', 'scheduler_interval', (string) ($automation['scheduler_interval'] ?? 15), $errors['scheduler_interval'] ?? null, 'number', true, [
            'min' => 1,
            'max' => 1440,
        ]);
        echo '<p class="help">Choose how often platform tasks should run. Use the system cron for predictable execution or rely on page loads when cron access is unavailable.</p>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=3">Back</a>';
        echo '<button type="submit">Continue to Finalize</button>';
        echo '</div>';
        echo '</form>';
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderFinalizeStep(array $errors): void
    {
        $config = $this->buildConfiguration();
        $networkSummary = [];
        if (!empty($config['networks']) && is_array($config['networks'])) {
            foreach ($config['networks'] as $candidate) {
                if (is_array($candidate)) {
                    $networkSummary = $candidate;
                    break;
                }
            }
        }

        echo '<form method="post">';
        echo '<h2>Finalize Setup</h2>';
        echo '<p>Review your settings below. Click "Finish" to write the configuration file and complete the setup.</p>';

        if (isset($errors['config'])) {
            echo '<p class="error">' . htmlspecialchars($errors['config'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        echo '<div class="summary">';
        echo '<h3>Platform</h3>';
        echo '<ul>';
        echo '<li>Name: ' . htmlspecialchars((string) ($config['app']['name'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Base URL: ' . htmlspecialchars((string) ($config['app']['base_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Force HTTPS: ' . (!empty($config['app']['force_https']) ? 'Yes' : 'No') . '</li>';
        echo '</ul>';

        echo '<h3>Database</h3>';
        echo '<ul>';
        echo '<li>Host: ' . htmlspecialchars((string) ($config['database']['host'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Port: ' . htmlspecialchars((string) ($config['database']['port'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Name: ' . htmlspecialchars((string) ($config['database']['name'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Username: ' . htmlspecialchars((string) ($config['database']['user'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '</ul>';

        echo '<h3>Super Administrator</h3>';
        echo '<ul>';
        echo '<li>Email: ' . htmlspecialchars((string) ($config['super_admin']['email'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '</ul>';

        if (!empty($networkSummary)) {
            echo '<h3>Network</h3>';
            echo '<ul>';
            echo '<li>Name: ' . htmlspecialchars((string) ($networkSummary['name'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
            echo '<li>Primary URL: ' . htmlspecialchars((string) ($networkSummary['primary_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
            $aliases = $networkSummary['aliases'] ?? [];
            $aliasText = empty($aliases) ? 'None' : implode(', ', $aliases);
            echo '<li>Aliases: ' . htmlspecialchars($aliasText, ENT_QUOTES, 'UTF-8') . '</li>';
            $auto = $networkSummary['auto_updates'] ?? [];
            echo '<li>Automatic Updates: ' . (!empty($auto['enabled']) ? 'Enabled' : 'Disabled') . '</li>';
            echo '<li>Super Admin Deployments: ' . (!empty($auto['allow_super_admin_deployments']) ? 'Allowed' : 'Blocked') . '</li>';
            echo '</ul>';
        }

        echo '<h3>Automation</h3>';
        echo '<ul>';
        echo '<li>Auto Deploy: ' . (!empty($config['updates']['auto_deploy']) ? 'Enabled' : 'Disabled') . '</li>';
        echo '<li>Updates Branch: ' . htmlspecialchars((string) ($config['updates']['branch'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Network Opt-out Allowed: ' . (!empty($config['updates']['allow_network_opt_out']) ? 'Yes' : 'No') . '</li>';
        echo '<li>Scheduler Mode: ' . htmlspecialchars((string) ($config['scheduler']['mode'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Scheduler Interval: ' . htmlspecialchars((string) ($config['scheduler']['interval_minutes'] ?? ''), ENT_QUOTES, 'UTF-8') . ' minutes</li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=4">Back</a>';
        echo '<button type="submit">Finish Setup</button>';
        echo '</div>';
        echo '</form>';
    }
    private function renderField(string $label, string $name, string $value, ?string $error = null, string $type = 'text', bool $required = true, array $attributes = []): string
    {
        $html = '<label>';
        $html .= '<span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';

        $attributes = array_merge([
            'type' => $type,
            'name' => $name,
            'value' => $value,
        ], $attributes);

        if ($type === 'number') {
            $attributes += ['min' => $attributes['min'] ?? 1, 'max' => $attributes['max'] ?? 65535];
        }

        $parts = [];
        foreach ($attributes as $attribute => $attributeValue) {
            if ($attributeValue === null) {
                continue;
            }

            if (is_bool($attributeValue)) {
                if ($attributeValue) {
                    $parts[] = htmlspecialchars($attribute, ENT_QUOTES, 'UTF-8');
                }
                continue;
            }

            $parts[] = htmlspecialchars((string) $attribute, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $attributeValue, ENT_QUOTES, 'UTF-8') . '"';
        }

        if ($required) {
            $parts[] = 'required';
        }

        $html .= '<input ' . implode(' ', $parts) . '>';

        if ($error) {
            $html .= '<small class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</small>';
        }

        $html .= '</label>';

        return $html;
    }

    private function renderTextareaField(string $label, string $name, string $value, ?string $error = null, bool $required = false, array $attributes = []): string
    {
        $rows = isset($attributes['rows']) ? (int) $attributes['rows'] : 3;
        unset($attributes['rows']);
        if ($rows < 1) {
            $rows = 1;
        }

        $attributes = array_merge([
            'name' => $name,
            'rows' => (string) $rows,
        ], $attributes);

        $parts = [];
        foreach ($attributes as $attribute => $attributeValue) {
            if ($attributeValue === null) {
                continue;
            }

            if (is_bool($attributeValue)) {
                if ($attributeValue) {
                    $parts[] = htmlspecialchars($attribute, ENT_QUOTES, 'UTF-8');
                }
                continue;
            }

            $parts[] = htmlspecialchars((string) $attribute, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $attributeValue, ENT_QUOTES, 'UTF-8') . '"';
        }

        if ($required) {
            $parts[] = 'required';
        }

        $html = '<label>';
        $html .= '<span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
        $html .= '<textarea ' . implode(' ', $parts) . '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</textarea>';
        if ($error) {
            $html .= '<small class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</small>';
        }
        $html .= '</label>';

        return $html;
    }

    /**
     * @return list<string>
     */
    private function getRequirementWarnings(): array
    {
        $warnings = [];

        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $warnings[] = 'PHP 8.2 or newer is recommended. The detected version is ' . PHP_VERSION . '.';
        }

        if (!extension_loaded('pdo_mysql')) {
            $warnings[] = 'The pdo_mysql extension is required for database access.';
        }

        if (!extension_loaded('openssl')) {
            $warnings[] = 'The OpenSSL extension is recommended for secure HTTPS connections.';
        }

        if (!extension_loaded('mbstring')) {
            $warnings[] = 'The mbstring extension is recommended for multi-byte text handling.';
        }

        return $warnings;
    }

    private function getStyles(): string
    {
        return <<<CSS
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 2rem;
            color: #1f2933;
        }

        .wizard {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
            padding: 2rem 2.5rem;
        }

        h1 {
            margin-top: 0;
            font-size: 2rem;
        }

        h2 {
            margin-top: 0;
            font-size: 1.5rem;
        }

        form {
            display: grid;
            gap: 1.5rem;
        }

        label {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-weight: 600;
        }

        label span {
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="url"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        textarea,
        select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 1rem;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input[type="text"]:focus,
        input[type="url"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        select {
            background: #fff;
        }

        .checkbox {
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .checkbox input {
            width: auto;
        }

        .section {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            border: 1px solid #e2e8f0;
            display: grid;
            gap: 1rem;
        }

        .section h3 {
            margin: 0;
            font-size: 1.15rem;
        }

        .help {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 400;
            color: #475569;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .actions button,
        .button {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .actions .button.secondary,
        .button.secondary {
            background: #e2e8f0;
            color: #1e293b;
        }

        .actions button:hover,
        .button:hover {
            background: #1d4ed8;
        }

        .button.secondary:hover {
            background: #cbd5f5;
        }

        .error {
            color: #b91c1c;
            font-size: 0.875rem;
        }

        .progress {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
            gap: 0.75rem;
        }

        .progress li {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background: #e2e8f0;
            text-align: center;
            font-weight: 600;
            color: #475569;
        }

        .progress li.current {
            background: #2563eb;
            color: #fff;
        }

        .progress li.complete {
            background: #10b981;
            color: #fff;
        }

        .detected,
        .summary {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .detected ul,
        .summary ul {
            margin: 0.5rem 0 0;
            padding-left: 1.25rem;
        }

        .requirements {
            margin-top: 1rem;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .requirements ul {
            margin: 0.5rem 0 0;
            padding-left: 1.25rem;
        }

        .requirements li {
            color: #c2410c;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .wizard {
                padding: 1.5rem;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .actions .button,
            .actions button {
                width: 100%;
            }
        }
        CSS;
    }

    private function detectBasePath(): string
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName === '') {
            return '/';
        }

        $scriptName = str_replace('\\', '/', $scriptName);
        $scriptDir = trim(dirname($scriptName), '/');
        if ($scriptDir === '' || $scriptDir === '.') {
            $scriptDir = '';
        } else {
            $scriptDir = '/' . $scriptDir;
        }

        if ($scriptDir !== '' && str_ends_with($scriptDir, '/setup')) {
            $scriptDir = substr($scriptDir, 0, -strlen('/setup'));
            $scriptDir = rtrim($scriptDir, '/');
            if ($scriptDir !== '') {
                $scriptDir = '/' . ltrim($scriptDir, '/');
            }
        }

        if ($scriptDir === '' || $scriptDir === '/') {
            return '/';
        }

        return $scriptDir;
    }

    /**
     * @return string|null
     */
    private function normalizeBaseUrl(string $baseUrl): ?string
    {
        $trimmed = trim($baseUrl);
        if ($trimmed === '') {
            return null;
        }

        $parts = parse_url($trimmed);
        if ($parts === false || !is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower((string) $parts['scheme']);
        $host = strtolower((string) $parts['host']);
        $https = $scheme === 'https';

        $port = null;
        if (isset($parts['port'])) {
            $port = (int) $parts['port'];
            if ($port <= 0) {
                $port = null;
            }
        }

        if ($port !== null && $this->isDefaultPort($https, $port)) {
            $port = null;
        }

        $path = (string) ($parts['path'] ?? '');
        if ($path !== '') {
            $path = rtrim($path, '/');
            if ($path === '' || $path === '/') {
                $path = '';
            } elseif ($path[0] !== '/') {
                $path = '/' . $path;
            }
        }

        $authority = $this->formatHostForUrl($host);
        if ($port !== null) {
            $authority .= ':' . $port;
        }

        return $scheme . '://' . $authority . $path;
    }

    private function normalizeBasePath(string $basePath): ?string
    {
        $trimmed = trim($basePath);
        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        if ($trimmed[0] !== '/') {
            $trimmed = '/' . $trimmed;
        }

        $normalized = rtrim($trimmed, '/');
        if ($normalized === '') {
            $normalized = '/';
        }

        if (!preg_match('~^/[A-Za-z0-9/_\-\.]*$~', $normalized)) {
            return null;
        }

        return $normalized === '' ? '/' : $normalized;
    }

    /**
     * @return list<string>
     */
    private function normalizeAliases(string $aliasesRaw): array
    {
        if ($aliasesRaw === '') {
            return [];
        }

        $parts = preg_split('/[\r\n,]+/', $aliasesRaw) ?: [];
        $aliases = [];

        foreach ($parts as $part) {
            $host = strtolower(trim($part));
            if ($host === '') {
                continue;
            }

            if (str_starts_with($host, 'http://') || str_starts_with($host, 'https://')) {
                $host = preg_replace('~^https?://~i', '', $host) ?? $host;
            }

            $host = trim($host, '/');
            if ($host === '') {
                continue;
            }

            $aliases[] = $host;
        }

        return array_values(array_unique($aliases));
    }

    private function isValidHost(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        if ($host === 'localhost' || $this->isIpAddress($host)) {
            return true;
        }

        if (str_contains($host, '/')) {
            return false;
        }

        return (bool) preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?))*$/i', $host);
    }

    private function sanitizeDetectedHost(string $host): string
    {
        $host = strtolower(trim($host));

        if ($host === '' || $host === '.') {
            return 'localhost';
        }

        $host = rtrim($host, '.');
        if ($host === '') {
            return 'localhost';
        }

        if ($host === 'localhost' || $this->isIpAddress($host)) {
            return $host;
        }

        if (!$this->isValidHost($host)) {
            return 'localhost';
        }

        return $host;
    }

    private function deriveBaseDomain(string $host): string
    {
        if ($host === '' || $host === 'localhost' || $this->isIpAddress($host)) {
            return $host === '' ? 'localhost' : $host;
        }

        $parts = explode('.', $host);
        if (count($parts) < 2) {
            return $host;
        }

        $tld = array_pop($parts);
        $second = array_pop($parts);
        if ($second === null) {
            return $host;
        }

        $candidate = $second . '.' . $tld;

        if (!empty($parts)) {
            $suffix = strtolower($candidate);
            if (in_array($suffix, self::MULTI_LEVEL_TLDS, true)) {
                $thirdLevel = array_pop($parts);
                if ($thirdLevel !== null && $thirdLevel !== '') {
                    return $thirdLevel . '.' . $candidate;
                }
            }
        }

        return $candidate;
    }

    private function deriveSlugFromHost(string $host, string $baseDomain): string
    {
        if ($host === '' || $host === $baseDomain || $this->isIpAddress($host)) {
            return 'network';
        }

        $suffix = '.' . $baseDomain;
        if (str_ends_with($host, $suffix)) {
            $prefix = substr($host, 0, -strlen($suffix));
        } else {
            $prefix = $host;
        }

        $segments = array_filter(explode('.', $prefix));
        if (empty($segments)) {
            return 'network';
        }

        $candidate = (string) end($segments);
        $candidate = preg_replace('/[^a-z0-9-]/i', '-', $candidate) ?? 'network';
        $candidate = trim($candidate, '-');

        if ($candidate === '') {
            $candidate = 'network';
        }

        return strtolower($candidate);
    }

    private function isIpAddress(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    private function isDefaultPort(bool $https, int $port): bool
    {
        return ($https && $port === 443) || (!$https && $port === 80);
    }

    private function firstHeaderValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $parts = explode(',', $value);
        return trim((string) $parts[0]);
    }

    /**
     * @return array{0:string,1:int|null}
     */
    private function extractHostAndPort(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return ['', null];
        }

        if (str_starts_with($value, '[')) {
            $end = strpos($value, ']');
            if ($end !== false) {
                $host = substr($value, 1, $end - 1);
                $portPart = substr($value, $end + 1);
                if (str_starts_with($portPart, ':')) {
                    $portPart = substr($portPart, 1);
                }
                $port = ctype_digit($portPart) ? (int) $portPart : null;

                return [$host, $port];
            }
        }

        if (substr_count($value, ':') > 1) {
            return [$value, null];
        }

        $colonPos = strrpos($value, ':');
        if ($colonPos === false) {
            return [$value, null];
        }

        $host = substr($value, 0, $colonPos);
        $portPart = substr($value, $colonPos + 1);
        if ($host === '') {
            $host = $value;
            $portPart = '';
        }

        $port = ctype_digit($portPart) ? (int) $portPart : null;

        return [$host, $port];
    }

    private function formatHostForUrl(string $host): string
    {
        if ($host === '') {
            return 'localhost';
        }

        if ($this->isIpAddress($host) && str_contains($host, ':')) {
            return '[' . $host . ']';
        }

        return $host;
    }
}
