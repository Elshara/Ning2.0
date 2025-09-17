<?php
declare(strict_types=1);

session_start();

$baseDir = dirname(__DIR__);
$configPath = $baseDir . '/config/app.php';
$sessionKey = 'nf_setup_wizard';
$completed = !empty($_SESSION[$sessionKey]['completed']);

if (is_file($configPath) && !$completed) {
    header('Location: ../');
    exit;
}

$wizard = new SetupWizard($baseDir, $configPath);
$wizard->handle();

class SetupWizard
{
    private const SESSION_KEY = 'nf_setup_wizard';

    private string $baseDir;
    private string $configPath;
    /**
     * @var array<string,mixed>
     */
    private array $state;

    public function __construct(string $baseDir, string $configPath)
    {
        $this->baseDir = $baseDir;
        $this->configPath = $configPath;
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $detected = $this->detectEnvironment();
            $_SESSION[self::SESSION_KEY] = [
                'environment' => [
                    'site_name' => 'My Network',
                    'base_url' => $detected['base_url'] ?? '',
                    'force_https' => !empty($detected['https_detected']),
                    'detected' => $detected,
                ],
            ];
        }

        $this->state = &$_SESSION[self::SESSION_KEY];
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
        if ($step < 1 || $step > 4) {
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

        if ($currentStep >= 4) {
            return 'complete';
        }

        return $currentStep + 1;
    }

    private function redirectToStep(string $step): void
    {
        header('Location: ?step=' . urlencode($step));
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
                return $this->handleAdminStep();
            case 4:
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

        if ($siteName === '') {
            $errors['site_name'] = 'Please enter a name for your network.';
        }

        if ($baseUrl === '' || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $errors['base_url'] = 'Please provide a valid base URL (e.g. https://example.com).';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $this->state['environment']['site_name'] = $siteName;
        $this->state['environment']['base_url'] = $baseUrl;
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
    private function handleAdminStep(): array
    {
        $email = trim($_POST['admin_email'] ?? '');
        $password = (string) ($_POST['admin_password'] ?? '');
        $confirm = (string) ($_POST['admin_password_confirm'] ?? '');

        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['admin_email'] = 'Please provide a valid administrator email address.';
        }

        if (strlen($password) < 8) {
            $errors['admin_password'] = 'The administrator password must be at least 8 characters long.';
        }

        if (!hash_equals($password, $confirm)) {
            $errors['admin_password_confirm'] = 'The password confirmation does not match.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $this->state['admin'] = [
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
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

        if (empty($this->state['admin']['email']) || empty($this->state['admin']['password_hash'])) {
            return ['config' => 'Administrator details are missing. Please provide the administrator email and password.'];
        }

        $config = $this->buildConfiguration();
        $configDir = dirname($this->configPath);

        if (!is_dir($configDir) && !mkdir($configDir, 0755, true) && !is_dir($configDir)) {
            return ['config' => 'Unable to create configuration directory: ' . $configDir];
        }

        $configContents = "<?php\nreturn " . var_export($config, true) . ";\n";

        if (@file_put_contents($this->configPath, $configContents) === false) {
            return ['config' => 'Unable to write configuration file: ' . $this->configPath];
        }

        @chmod($this->configPath, 0640);

        $this->state['completed'] = true;
        unset($this->state['database'], $this->state['admin']);

        return [];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildConfiguration(): array
    {
        $environment = $this->state['environment'] ?? [];
        $database = $this->state['database'] ?? [];
        $admin = $this->state['admin'] ?? [];

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
            'admin' => $admin,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function detectEnvironment(): array
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
            || (($_SERVER['REQUEST_SCHEME'] ?? '') === 'https');

        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $port = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : ($https ? 443 : 80);

        $scheme = $https ? 'https' : 'http';
        $baseUrl = $scheme . '://' . $host;
        if (!($https && $port === 443) && !(!$https && $port === 80)) {
            $baseUrl .= ':' . $port;
        }

        return [
            'php_version' => PHP_VERSION,
            'extensions' => get_loaded_extensions(),
            'https_detected' => $https,
            'host' => $host,
            'port' => $port,
            'base_url' => $baseUrl,
        ];
    }

    /**
     * @param int|string $step
     * @param array<string,string> $errors
     */
    private function render($step, array $errors): void
    {
        header('Content-Type: text/html; charset=utf-8');
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
            echo '<p>Your network is configured and ready to go.</p>';
            echo '<p><a class="button" href="../">Launch My Network</a></p>';
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
            3 => 'Administrator',
            4 => 'Finalize',
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
                $this->renderAdminStep($errors);
                break;
            case 4:
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

        echo '<form method="post">';
        echo '<h2>Environment</h2>';
        echo '<p>We detected the following environment details. Update them if needed.</p>';

        echo $this->renderField('Site Name', 'site_name', $siteName, $errors['site_name'] ?? null);
        echo $this->renderField('Base URL', 'base_url', $baseUrl, $errors['base_url'] ?? null, 'url');

        echo '<label class="checkbox">';
        echo '<input type="checkbox" name="force_https" value="1"' . ($forceHttps ? ' checked' : '') . '>'; 
        echo ' Always use HTTPS';
        echo '</label>';

        echo '<div class="detected">';
        echo '<h3>Detected Settings</h3>';
        echo '<ul>';
        echo '<li>PHP Version: ' . htmlspecialchars($detected['php_version'] ?? PHP_VERSION, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>HTTPS: ' . (!empty($detected['https_detected']) ? 'Yes' : 'No') . '</li>';
        echo '<li>Host: ' . htmlspecialchars((string) ($detected['host'] ?? 'localhost'), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>Port: ' . htmlspecialchars((string) ($detected['port'] ?? 80), ENT_QUOTES, 'UTF-8') . '</li>';
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

        $passwordError = $errors['db_password'] ?? null;
        echo '<label>';
        echo '<span>Password</span>';
        echo '<input type="password" name="db_password" value="' . htmlspecialchars((string) $database['password'], ENT_QUOTES, 'UTF-8') . '">';
        if ($passwordError) {
            echo '<small class="error">' . htmlspecialchars($passwordError, ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        if (isset($errors['connection'])) {
            echo '<p class="error">' . htmlspecialchars($errors['connection'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=1">Back</a>';
        echo '<button type="submit">Continue to Administrator</button>';
        echo '</div>';
        echo '</form>';
    }

    /**
     * @param array<string,string> $errors
     */
    private function renderAdminStep(array $errors): void
    {
        $admin = $this->state['admin'] ?? [
            'email' => '',
        ];

        echo '<form method="post">';
        echo '<h2>Administrator Account</h2>';
        echo '<p>Set up the initial administrator account for your network.</p>';

        echo $this->renderField('Email Address', 'admin_email', (string) ($admin['email'] ?? ''), $errors['admin_email'] ?? null, 'email');

        echo '<label>';
        echo '<span>Password</span>';
        echo '<input type="password" name="admin_password" value="">';
        if (isset($errors['admin_password'])) {
            echo '<small class="error">' . htmlspecialchars($errors['admin_password'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        echo '<label>';
        echo '<span>Confirm Password</span>';
        echo '<input type="password" name="admin_password_confirm" value="">';
        if (isset($errors['admin_password_confirm'])) {
            echo '<small class="error">' . htmlspecialchars($errors['admin_password_confirm'], ENT_QUOTES, 'UTF-8') . '</small>';
        }
        echo '</label>';

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=2">Back</a>';
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

        echo '<form method="post">';
        echo '<h2>Finalize Setup</h2>';
        echo '<p>Review your settings below. Click "Finish" to write the configuration file and complete the setup.</p>';

        if (isset($errors['config'])) {
            echo '<p class="error">' . htmlspecialchars($errors['config'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        echo '<div class="summary">';
        echo '<h3>Application</h3>';
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

        echo '<h3>Administrator</h3>';
        echo '<ul>';
        echo '<li>Email: ' . htmlspecialchars((string) ($config['admin']['email'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<a class="button secondary" href="?step=3">Back</a>';
        echo '<button type="submit">Finish Setup</button>';
        echo '</div>';
        echo '</form>';
    }

    private function renderField(string $label, string $name, string $value, ?string $error = null, string $type = 'text', bool $required = true): string
    {
        $html = '<label>';
        $html .= '<span>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
        $attributes = [
            'type="' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '"',
            'name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"',
            'value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"',
        ];

        if ($type === 'number') {
            $attributes[] = 'min="1"';
            $attributes[] = 'max="65535"';
        }

        if ($required) {
            $attributes[] = 'required';
        }

        $html .= '<input ' . implode(' ', $attributes) . '>';
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
            max-width: 720px;
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
            gap: 1.25rem;
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
        input[type="number"] {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 1rem;
        }

        input[type="text"]:focus,
        input[type="url"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
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

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
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
                gap: 1rem;
            }

            .actions .button.secondary {
                width: 100%;
            }

            .actions button {
                width: 100%;
            }
        }
        CSS;
    }
}
