<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2018
 * @author: bugbear
 * @date: 2018/3/30
 * @time: 下午4:41
 */

require './vendor/autoload.php';

define('ROOT', __DIR__);

use Lily\Parser;

$root = ROOT . '/impress';
$lily = new Parser($root, 'impress.yaml');

$lily->run();