<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2018
 * @author: bugbear
 * @date: 2018/3/30
 * @time: 下午4:41
 */

define('ROOT', __DIR__);

require dirname(ROOT) . '/vendor/autoload.php';


use Lily\Parser;

$root = ROOT;
$lily = new Parser($root, 'entry.yaml');

$data = $lily->run();

var_export($data);
