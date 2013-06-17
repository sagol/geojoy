<?php

$config = require(dirname(__FILE__) . DS . 'main.php');

$console = array(
	'basePath' => dirname(__FILE__) . DS . '..',
	'name' => 'Geo Joy Console',

	'commandMap'=>array(
		'migrate' => array(
			'class' => 'system.cli.commands.MigrateCommand',
			'migrationTable' => 'migrations',
		),
	),

	'components' => array(
		'mutex' => array(
			'class' => 'application.extensions.EMutex',
		),
	),

	'params' => array(
		// папка для загрузки файлов на сервер
		'uploadDir' => 'webroot.upload',

		'sendMailsCount' => 10,
	),
);

$console['sourceLanguage'] = $config['sourceLanguage'];
$console['language'] = $config['language'];

$console['components']['db'] = $config['components']['db'];
$console['components']['mailer'] = $config['components']['mailer'];
$console['components']['appLog'] = $config['components']['appLog'];
$console['components']['cache'] = $config['components']['cache'];

$console['params']['lang'] = $config['params']['lang'];

unset($config);

return $console;