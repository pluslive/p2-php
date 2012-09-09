<?php
/**
 * rep2 expack console script
 */
define('P2_CLI_RUN', 1);

chdir(dirname(__DIR__));
require __DIR__ . '/../init.php';
require P2EX_LIB_DIR . '/Console/Application.php';

use expack\Console\Application;

error_reporting(E_ALL);
$app = new Application();
$app->run();
