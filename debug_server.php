<?php
/**
 * Debug diagnostic page - TEMPORARY - DELETE AFTER USE
 * Upload to checkpointGate/ folder and access via browser
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Step 1: Check basic server info
$checks = [];

// PHP Version
$checks['php_version'] = phpversion();
$checks['php_ok'] = version_compare(phpversion(), '8.2.0', '>=');

// Key paths
$checks['document_root'] = $_SERVER['DOCUMENT_ROOT'] ?? 'N/A';
$checks['script_filename'] = $_SERVER['SCRIPT_FILENAME'] ?? 'N/A';
$checks['current_dir'] = __DIR__;
$checks['request_uri'] = $_SERVER['REQUEST_URI'] ?? 'N/A';
$checks['script_name'] = $_SERVER['SCRIPT_NAME'] ?? 'N/A';

// Check critical files
$checks['env_exists'] = file_exists(__DIR__.'/.env');
$checks['vendor_exists'] = file_exists(__DIR__.'/vendor/autoload.php');
$checks['bootstrap_exists'] = file_exists(__DIR__.'/bootstrap/app.php');
$checks['public_index_exists'] = file_exists(__DIR__.'/public/index.php');
$checks['routes_exists'] = file_exists(__DIR__.'/routes/web.php');
$checks['storage_writable'] = is_writable(__DIR__.'/storage');
$checks['cache_writable'] = is_writable(__DIR__.'/bootstrap/cache');

// Check .env content
$envContent = '';
$envVars = [];
if ($checks['env_exists']) {
    $envContent = file_get_contents(__DIR__.'/.env');
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            if (in_array($key, ['APP_ENV','APP_DEBUG','APP_KEY','APP_URL','DB_CONNECTION','DB_HOST','DB_DATABASE','DB_USERNAME','SESSION_DRIVER','CACHE_STORE'])) {
                // Mask sensitive values
                if (in_array($key, ['APP_KEY','DB_USERNAME'])) {
                    $envVars[$key] = strlen(trim($val, '"\'')) > 0 ? '*** SET ***' : 'EMPTY';
                } else {
                    $envVars[$key] = trim($val, '"\'');
                }
            }
        }
    }
}

// Try to bootstrap Laravel and capture the error
$laravelError = null;
$laravelRoutes = [];
$laravelStatus = null;
$laravelDebugPath = null;
$laravelBaseUrl = null;

if ($checks['vendor_exists'] && $checks['bootstrap_exists']) {
    try {
        define('LARAVEL_START', microtime(true));

        if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
            require $maintenance;
        }

        require __DIR__.'/vendor/autoload.php';

        $app = require_once __DIR__.'/bootstrap/app.php';

        $request = \Illuminate\Http\Request::capture();

        $laravelDebugPath = $request->getPathInfo();
        $laravelBaseUrl = $request->getBaseUrl();

        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);

        $laravelStatus = $response->getStatusCode();

        // Get routes
        try {
            $routes = app('router')->getRoutes();
            foreach ($routes as $route) {
                $laravelRoutes[] = implode('|', $route->methods()) . ' ' . $route->uri();
            }
        } catch (\Throwable $e) {
            $laravelRoutes[] = 'ERROR: ' . $e->getMessage();
        }

        // If error, capture exception from response
        if ($laravelStatus >= 400) {
            $laravelError = "HTTP {$laravelStatus} - Check path and routes below";
            // Try to get exception from response content
            $content = strip_tags($response->getContent());
            if ($content) {
                $laravelError .= "\nResponse: " . substr($content, 0, 500);
            }
        }

    } catch (\Throwable $e) {
        $laravelError = get_class($e) . ': ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine() . "\n\nStack Trace:\n" . $e->getTraceAsString();
    }
}

// Check PHP extensions
$requiredExtensions = ['mbstring', 'xml', 'curl', 'pdo_mysql', 'fileinfo', 'tokenizer', 'json', 'openssl'];
$extensionStatus = [];
foreach ($requiredExtensions as $ext) {
    $extensionStatus[$ext] = extension_loaded($ext);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkpoint GIIC - Server Diagnostic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; padding: 20px; }
        h1 { color: #38bdf8; margin-bottom: 20px; font-size: 24px; }
        h2 { color: #94a3b8; font-size: 16px; margin: 20px 0 10px; text-transform: uppercase; letter-spacing: 1px; }
        .card { background: #1e293b; border-radius: 8px; padding: 16px; margin-bottom: 16px; border: 1px solid #334155; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #334155; }
        .row:last-child { border-bottom: none; }
        .label { color: #94a3b8; font-size: 13px; }
        .value { font-family: monospace; font-size: 13px; }
        .ok { color: #4ade80; }
        .fail { color: #f87171; }
        .warn { color: #fbbf24; }
        pre { background: #0f172a; padding: 12px; border-radius: 6px; font-size: 12px; overflow-x: auto; white-space: pre-wrap; word-break: break-all; margin-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-ok { background: #166534; color: #4ade80; }
        .badge-fail { background: #7f1d1d; color: #f87171; }
    </style>
</head>
<body>
    <h1>🔍 Checkpoint GIIC — Server Diagnostic</h1>

    <!-- SERVER INFO -->
    <h2>Server Info</h2>
    <div class="card">
        <div class="row"><span class="label">PHP Version</span><span class="value <?= $checks['php_ok'] ? 'ok' : 'fail' ?>"><?= $checks['php_version'] ?> <?= $checks['php_ok'] ? '✅' : '❌ Requires 8.2+' ?></span></div>
        <div class="row"><span class="label">Document Root</span><span class="value"><?= $checks['document_root'] ?></span></div>
        <div class="row"><span class="label">Script Filename</span><span class="value"><?= $checks['script_filename'] ?></span></div>
        <div class="row"><span class="label">Current Dir</span><span class="value"><?= $checks['current_dir'] ?></span></div>
        <div class="row"><span class="label">Request URI</span><span class="value"><?= $checks['request_uri'] ?></span></div>
        <div class="row"><span class="label">Script Name</span><span class="value"><?= $checks['script_name'] ?></span></div>
    </div>

    <!-- FILE CHECKS -->
    <h2>File & Permission Checks</h2>
    <div class="card">
        <?php
        $fileChecks = [
            '.env' => $checks['env_exists'],
            'vendor/autoload.php' => $checks['vendor_exists'],
            'bootstrap/app.php' => $checks['bootstrap_exists'],
            'public/index.php' => $checks['public_index_exists'],
            'routes/web.php' => $checks['routes_exists'],
            'storage/ (writable)' => $checks['storage_writable'],
            'bootstrap/cache/ (writable)' => $checks['cache_writable'],
        ];
        foreach ($fileChecks as $name => $status): ?>
        <div class="row">
            <span class="label"><?= $name ?></span>
            <span class="badge <?= $status ? 'badge-ok' : 'badge-fail' ?>"><?= $status ? 'OK' : 'MISSING / NOT WRITABLE' ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ENV CONFIG -->
    <h2>.env Configuration</h2>
    <div class="card">
        <?php if (empty($envVars)): ?>
            <div class="row"><span class="fail">⚠️ .env file not found or empty!</span></div>
        <?php else: ?>
            <?php foreach ($envVars as $key => $val): ?>
            <div class="row">
                <span class="label"><?= $key ?></span>
                <span class="value <?= (empty($val) || $val === 'EMPTY') ? 'fail' : 'ok' ?>"><?= htmlspecialchars($val) ?></span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- PHP EXTENSIONS -->
    <h2>PHP Extensions</h2>
    <div class="card">
        <?php foreach ($extensionStatus as $ext => $loaded): ?>
        <div class="row">
            <span class="label"><?= $ext ?></span>
            <span class="badge <?= $loaded ? 'badge-ok' : 'badge-fail' ?>"><?= $loaded ? 'Loaded' : 'MISSING' ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- LARAVEL STATUS -->
    <h2>Laravel Bootstrap Test</h2>
    <div class="card">
        <?php if ($laravelError): ?>
            <div class="row"><span class="label">Status</span><span class="fail">❌ ERROR</span></div>
            <div class="row"><span class="label">Computed Path</span><span class="value"><?= htmlspecialchars($laravelDebugPath ?? 'N/A') ?></span></div>
            <div class="row"><span class="label">Computed Base URL</span><span class="value"><?= htmlspecialchars($laravelBaseUrl ?? 'N/A') ?></span></div>
            <div class="row"><span class="label">HTTP Status</span><span class="value fail"><?= $laravelStatus ?? 'N/A' ?></span></div>
            <pre class="fail"><?= htmlspecialchars($laravelError) ?></pre>
        <?php else: ?>
            <div class="row"><span class="label">Status</span><span class="ok">✅ Laravel OK (HTTP <?= $laravelStatus ?>)</span></div>
            <div class="row"><span class="label">Computed Path</span><span class="value ok"><?= htmlspecialchars($laravelDebugPath) ?></span></div>
            <div class="row"><span class="label">Computed Base URL</span><span class="value ok"><?= htmlspecialchars($laravelBaseUrl) ?></span></div>
        <?php endif; ?>
    </div>

    <!-- ROUTES -->
    <?php if (!empty($laravelRoutes)): ?>
    <h2>Registered Routes</h2>
    <div class="card">
        <pre><?php foreach ($laravelRoutes as $route): ?>
<?= htmlspecialchars($route) ?>
<?php endforeach; ?></pre>
    </div>
    <?php endif; ?>

    <p style="color: #475569; margin-top: 20px; font-size: 12px;">⚠️ DELETE this file after debugging! — Generated <?= date('Y-m-d H:i:s') ?></p>
</body>
</html>
