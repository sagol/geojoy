<?php

$dirname = dirname(__FILE__);

// $startTime = microtime(true); 
// xdebug_start_trace($dirname . '/../protected/runtime/xdebug' . $startTime);

define('DS', DIRECTORY_SEPARATOR);
defined('SITE_PATH') or define('SITE_PATH', $dirname);

$yii = $dirname . DS . '..' . DS . 'yiiframework' . DS . 'framework' . DS . 'yii.php';
// $yii = $dirname . DS . '..' . DS . 'yiiframework' . DS . 'framework' . DS . 'yiilite.php';
$config = $dirname . DS . '..' . DS . 'protected' . DS . 'config' . DS . 'main.php';

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

if(YII_DEBUG) error_reporting(E_ALL | E_STRICT);
else error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

require_once($yii);
require_once($dirname . DS . '..' . DS . 'protected' . DS . 'components' . DS . 'core' . DS . 'WebApplication.php');

Yii::createApplication('\app\components\core\WebApplication', $config)->run();

// получаем время окончания
// $endTime = microtime(true);

// выводим время.
// echo $fullTime = $endTime - $startTime;

// xdebug_stop_trace($dirname . '/../protected/runtime/xdebug' . $startTime);