<?php

require_once __DIR__.'/../vendor/autoload.php';

defined('CORE_ROOT_DIR') or define('CORE_ROOT_DIR', __DIR__ . '/..');
defined('CORE_APP_DIR') or define('CORE_APP_DIR', CORE_ROOT_DIR . '/app');
defined('CORE_CACHE_DIR') or define('CORE_CACHE_DIR', CORE_ROOT_DIR. '/var/cache');
defined('CORE_DATA_DIR') or define('CORE_DATA_DIR', CORE_ROOT_DIR. '/data');

$app = new \App\Application();

return $app;
