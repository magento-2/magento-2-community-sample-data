<?php
// @codingStandardsIgnoreFile
// other bootstrap
define('PROJECT_ROOT', dirname(__DIR__ . '/../../../'));
define('TEST_ROOT', PROJECT_ROOT . '/Test');
require_once PROJECT_ROOT . '/vendor/autoload.php';
$RELATIVE_FW_PATH = '/vendor/magento/magento2-functional-testing-framework';

if (file_exists(TEST_ROOT . '/.env')) {
    $env = new \Dotenv\Loader(TEST_ROOT . '/.env');
    $env->load();

    if (array_key_exists('TESTS_MODULE_PATH', $_ENV) xor array_key_exists('TESTS_BP', $_ENV)) {
        throw new Exception('You must define both parameters TESTS_BP and TESTS_MODULE_PATH or neither parameter');
    }
    foreach ($_ENV as $key => $var) {
        defined($key) || define($key, $var);
    }
}

defined('FW_BP') || define('FW_BP', PROJECT_ROOT . $RELATIVE_FW_PATH);

$debug_mode = isset($_ENV['MFTF_DEBUG']) ? $_ENV['MFTF_DEBUG'] : false;
if (!(bool)$debug_mode && extension_loaded('xdebug')) {
    xdebug_disable();
}
