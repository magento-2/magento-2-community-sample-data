<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Code\Generator\Io;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\TestFramework\Unit\Autoloader\ExtensionAttributesGenerator;
use Magento\Framework\TestFramework\Unit\Autoloader\ExtensionAttributesInterfaceGenerator;
use Magento\Framework\TestFramework\Unit\Autoloader\FactoryGenerator;
use Magento\Framework\TestFramework\Unit\Autoloader\GeneratedClassesAutoloader;

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', dirname(__DIR__) . '/tmp/unit');
}

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
function __()
{
    $argc = func_get_args();

    $text = array_shift($argc);
    if (!empty($argc) && is_array($argc[0])) {
        $argc = $argc[0];
    }

    return new \Magento\Framework\Phrase($text, $argc);
}

$generatorIo = new Io(
    new File(),
    TESTS_TEMP_DIR . '/var/generation'
);
$generatedCodeAutoloader = new GeneratedClassesAutoloader(
    [
        new ExtensionAttributesGenerator(),
        new ExtensionAttributesInterfaceGenerator(),
        new FactoryGenerator(),
    ],
    $generatorIo
);
spl_autoload_register([$generatedCodeAutoloader, 'load']);
setCustomErrorHandler();

\Magento\Framework\Phrase::setRenderer(new \Magento\Framework\Phrase\Renderer\Placeholder());
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*  For data consistency between displaying (printing) and serialization a float number */
ini_set('precision', 14);
ini_set('serialize_precision', 14);

/**
 * Set custom error handler
 */
function setCustomErrorHandler()
{
    set_error_handler(
        function ($errNo, $errStr, $errFile, $errLine) {
            if (error_reporting()) {
                $errorNames = [
                    E_ERROR => 'Error',
                    E_WARNING => 'Warning',
                    E_PARSE => 'Parse',
                    E_NOTICE => 'Notice',
                    E_CORE_ERROR => 'Core Error',
                    E_CORE_WARNING => 'Core Warning',
                    E_COMPILE_ERROR => 'Compile Error',
                    E_COMPILE_WARNING => 'Compile Warning',
                    E_USER_ERROR => 'User Error',
                    E_USER_WARNING => 'User Warning',
                    E_USER_NOTICE => 'User Notice',
                    E_STRICT => 'Strict',
                    E_RECOVERABLE_ERROR => 'Recoverable Error',
                    E_DEPRECATED => 'Deprecated',
                    E_USER_DEPRECATED => 'User Deprecated',
                ];

                $errName = isset($errorNames[$errNo]) ? $errorNames[$errNo] : "";

                throw new \PHPUnit\Framework\Exception(
                    sprintf("%s: %s in %s:%s.", $errName, $errStr, $errFile, $errLine),
                    $errNo
                );
            }
        }
    );
}
