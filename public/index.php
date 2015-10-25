<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

require_once __DIR__ . "/../vendor/autoload.php";

use Sainsbury\Application;
use Sainsbury\CurlWrapper;
use Sainsbury\Parser;

echo (new Application(new CurlWrapper(), new Parser()))->run();
