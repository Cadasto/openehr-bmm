<?php

/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

define('APP_NAME', 'template-repo');
define('APP_TITLE', 'Template Repo');
define('APP_VERSION', '0.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: 'info');

define('APP_DIR', dirname(__DIR__));
define('APP_DATA_DIR', (getenv('XDG_DATA_HOME') ?: '/tmp') . '/app');
