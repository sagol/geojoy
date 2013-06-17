<?php

$basePath = dirname(__FILE__);
$basePath = substr($basePath, 0, strrpos($basePath, '/'));

// соответствие имени класса файлу
Yii::$classMap['app\managers\Manager'] = $basidePath . '/models/fields/managers/Manager.php';
Yii::$classMap['app\managers\Object'] = $basePath . '/models/fields/managers/Object.php';
Yii::$classMap['app\managers\User'] = $basePath . '/models/fields/managers/User.php';

Yii::$classMap['app\managers\Managers'] = $basePath . '/models/fields/managers/Managers.php';
Yii::$classMap['app\managers\Objects'] = $basePath . '/models/fields/managers/Objects.php';

Yii::$classMap['app\fields\Field'] = $basePath . '/models/fields/Field.php';

// алиас для общего пространнства имен
Yii::setPathOfAlias('app', $basePath);


return array(
	'basePath' => $basePath,
	'name' => 'Geo Joy',
	'defaultController' => 'site/objects',
	'sourceLanguage' => 'code',
	// язык по умолчанию
	'language' => 'ru',

	// preloading 'log' component
	'preload' => array('log'),

	// autoloading model and component classes
	'import' => array(
		'app.models.*',
		'app.components.*',

		'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.services.*',
	),

	'modules' => array(
		'messages' => array(
			'class' => 'app\modules\messages\MessagesModule',
		),

		'emailField' => array(
			'class' => 'app\modules\emailField\EmailFieldModule',
		),
		'passField' => array(
			'class' => 'app\modules\passField\PassFieldModule',
		),
		'karmaField' => array(
			'class' => 'app\modules\karmaField\KarmaFieldModule',
		),
		'avatarField' => array(
			'class' => 'app\modules\avatarField\AvatarFieldModule',
		),
		'photoField' => array(
			'class' => 'app\modules\photoField\PhotoFieldModule',
		),
		'photosField' => array(
			'class' => 'app\modules\photosField\PhotosFieldModule',
			'maxUploadFiles' => 10,
		),
		'news' => array(
			'class' => 'app\modules\news\NewsModule',
			'newsOnPage' => 10,
		),
	),
	/*'onError' => function($event) {
		$event->handled = true;
	},*/
	/*'onBeginRequest' => function($event) {
	},*/

	// application components
	'components' => array(
		'request' => array(
			'class' => '\app\components\core\HttpRequest',
			// защита от CSRF-атаки
			'enableCsrfValidation' => true,
			// проверка подмена cookie
			'enableCookieValidation' => true,
			'noCsrfValidationRoutes' => array(
				'avatarField/webcam/upload',
			),
		),

		'urlManager' => array(
			'class' => '\app\components\core\UrlManager',
			'urlFormat' => 'path',
			'urlSuffix' => '.html',
			'showScriptName' => false,
			'rules' => array(
				'/<article:(contact|about)>' => 'site/site/pages',
				array(
					'class' => '\app\components\CategoryUrlRule',
				),

				'/<page:\d+>' => 'site/objects/index',
				// для красивой ссылки первой страницы
				'/' => 'site/objects/index',
				'/add' => 'site/objects/selectCategory',
				'/login' => 'site/user/login',
				'/logout' => 'site/user/logout',
				'/registration' => 'site/user/registration',
				'/search' => 'site/site/search',
				'/news' => 'news/show',
				'/news/<id:\d+>' => 'news/show/news',

				'/object/cancel/<new:\d+>/<id:\d+>' => 'site/objects/cancel/',
				'/object/edit/<id:\d+>' => 'site/objects/edit/',
				'/object/del/<id:\d+>' => 'site/objects/del/',
				'/object/up/<id:\d+>' => 'site/objects/objectUp/',
				'/object/<id:\d+>' => 'site/objects/view/',

				'/user/password' => 'passField/edit/index',
				'/user/password/ok' => 'passField/edit/editOk',
				'/user/karma/<id:\d+>' => 'karmaField/karma/index',

				'/user/messages/<filter:\w+>/thread/<id:\d+>' => 'messages/threads/thread',
				'/user/messages/thread/print/<id:\d+>' => 'messages/threads/print',
				'/user/messages/answer/<id:\d+>' => 'messages/threads/answer',
				'/user/messages/writer/<id:\d+>' => 'messages/threads/writer',
				'/user/messages/' => array('messages/threads/index', 'defaultParams' => array('filter' => 'all')),
				'/user/messages/contacts' => 'messages/contacts/index',
				'/user/messages/<filter:>' => 'messages/threads/index',
// 				'/messages' => 'messages/threads/index',

				'/password/recovery/' => 'site/user/recovery',

				'/user/' => 'site/user/objects',
				'/user/objects/<page:\d+>' => 'site/user/objects',
				'/user/<action:\w+>/<id:\d+>' => 'site/user/<action>',
				'/user/<action:\w+>' => 'site/user/<action>',

				'/bookmarks/' => 'site/bookmarks/index',
// 				'/bookmarks/show/<page:\d+>' => 'site/bookmarks/show',
// 				'/bookmarks/show' => 'site/bookmarks/show',
// 				'/bookmarks/show/<page:\w+>' => 'site/bookmarks/show',
				'/bookmarks/<action:\w+>/<id:\d+>/page/<page:\d+>' => 'site/bookmarks/<action>',
				'/bookmarks/<action:\w+>/<id:\d+>' => 'site/bookmarks/<action>',

				'/email/confirmation/ok' => 'emailField/confirmation/confirmationOk',
				'/email/confirmation' => 'emailField/confirmation/index',

				'/password/reset/ok' => 'passField/reset/resetOk',
				'/password/reset/step' => 'passField/reset/reset',
				'/password/reset' => 'passField/reset/index',

				/*'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',*/
			),
		),

		'user' => array(
			'class' => '\app\components\core\WebUser',
			// enable cookie-based authentication
			'allowAutoLogin' => true,
			'loginUrl' => array('/site/user/login'),
		),

		'authManager' => array(
			'class' => '\app\components\core\PhpAuthManager',
			// роль по умолчанию.
			'defaultRoles' => array('guest'),
		),

		'db' => array(
			// работа с postgresql через unix сокет
			// 'connectionString' => 'pgsql:host=/var/run/postgresql/;dbname=geojoy',

			'connectionString' => 'pgsql:host=localhost;port=5432;dbname=geojoy',
			'username' => 'geojoy',
			'password' => 'xxxxxxx',
			'charset' => 'utf8',
			'autoConnect' => false,
			// кеширование схемы базы
			'schemaCachingDuration' => 3600,
			// профилирование запросов (для отладки)
			'enableProfiling' => false,
			'enableParamLogging' => true,
		),

		'errorHandler' => array(
			'errorAction' => 'site/site/error',
		),

		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				/* вывод результатов профайлинга в браузер
				array(
					// направляем результаты профайлинга в ProfileLogRoute (отображается внизу страницы)
					'class' => 'CProfileLogRoute',
					'levels' => 'profile',
					'enabled' => true,
				), */
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'info, error, warning',
				),
				/* вывод ошибок в браузер
				array(
					'class' => 'CWebLogRoute',
					'levels' => 'error, warning, trace, info',
					// 'showInFireBug' => true,
				), */
				/* логирование запросов к базе в файл
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'trace',
					'categories' => 'system.db.CDbCommand',
					'LogFile' => 'db.trace',
					'maxFileSize' => 1024 * 100, //100 MB
				), */
			),
		),

		'cache' => array(
			/* кеш отключен */
			'class' => 'system.caching.CDummyCache',

			/* файловый кеш
			'class' => 'system.caching.CFileCache', */

			/* мемкеш 
			'class' => 'CMemCache',
			'servers' => array(
				array(
					'host' => 'localhost',
					'port' => 11211,
				),
			), */
			// переключение на работу с расширение php5-memcached
			// 'useMemcached' => true,
		),
		'messages' => array(
			// кеширование переводов из файла переводов в секундах
			'cachingDuration' => 24*60*60,
		),
		// расширение для авторизации по openid протоколу используется в ext.eauth.EAuth
		'loid' => array(
			'class' => 'ext.lightopenid.openid',
		),

		// расширение для авторизации в соцсетях
		'eauth' => array(
			'class' => 'ext.eauth.EAuth',
			'popup' => true, // Use the popup window instead of redirecting.
			'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache'.
			'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
			'services' => array( // You can change the providers and their classes.
				'google' => array(
					'class' => '\app\components\eauth\Google',
					'client_id' => 'xxxxxxxxxx.apps.googleusercontent.com',
					'client_secret' => 'xxxxxxxxxxxxxxxxx',
				),
				'twitter' => array(
					'class' => '\app\components\eauth\Twitter',
					'key' => 'xxxxxxxxxxxxxxxxxxx',
					'secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
				),
				'facebook' => array(
					'class' => '\app\components\eauth\Facebook',
					'client_id' => 'xxxxxxxxxxxxxxxxxxx', // так же указывается в  параметрах (facebookAppId), т.е. ниже
					'client_secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
				),
				'vkontakte' => array(
					'class' => '\app\components\eauth\VKontakte',
					'client_id' => 'xxxxxxxxxxx',
					'client_secret' => 'xxxxxxxxxxxxxxxxxxxxxxxx',
				),
			),
		),

		// расширение для отправки почты
		'mailer' => array(
			'class' => 'app.extensions.mailer.EMailer',

			/* локальная отправка */
			/*'Mailer' => 'mail', // smtp/mail/sendmail
			'FromName' => 'Geo Joy',
			'From' => 'reg@geojoy.com',
			'CharSet' => 'UTF-8',*/

			/* отправка через smtp с авторизацией */
			'Mailer' => 'smtp', // smtp/mail/sendmail
			'FromName' => 'Geo Joy',
			'From' => 'reg@xxxxxxx.com',
			'CharSet' => 'UTF-8',
			'Host' => 'smtp.xxxxxxx.ru',
			'SMTPAuth' => true,
			'Username' => 'reg@xxxxxxx.com',
			'Password' => 'xxxxxxxxxxxxxxxxx',
		),

		'appLog' => array(
			'class' => '\app\components\AppLog',
			// пропуск логирования для события user для укзанных title
			// 'skipTitle' => array('user' => array('Вход пользователя', 'Выход пользователя')) 
			// пропуск событий mail
			// 'skipActions' => array('mail'),
			// пропуск событий типа info (константы \app\components\AppLog::TYPE_*)
			// 'skipType' => array(0),
			// пропуск всех событий с titl`ом test
			// 'skipTitle' => array('test') 
			// пропуск лога для события user если у него title - Пользователь удален
			// 'skipTitle' => array('user' => array('Пользователь удален')) 
		),

		'message' => array(
			'class' => '\app\components\Message',
		),
	),

	// application-level parameters that can be accessed
	// using \Yii::app()->params['paramName']
	'params' => array(
		// продолжительность показа всплывающего сообщения
		'messageShowTime' => 60000, // минута

		// папка для загрузки файлов на сервер
		'uploadDir' => 'webroot.upload',
		// урл для загрузки файлов от корня сервера
		'uploadUrl' => '/upload',

		'defaultMapInterface' => 'g', // g - google, y - yandex

		'facebookAppId' => 'xxxxxxxxxxxxxxxxxx',

		'googleTranslateApiKey' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',

		// Screen name of the user to attribute the Tweet to
		'twitterName' => '',

		// поддерживаемые языки. на данный момент порядок нельзя менять, после запуска сайта/создания объявлений
		// по расположению определяется на каком языке хранится текст
		'lang' => array('ru' => 'ru', 'en' => 'en'),
		// желаемая сортировка языков на сайте
		'langOrder' => array('ru' => 'ru', 'en' => 'en'),
		// соответствие языка сайта, языку facebook`а (http://developers.facebook.com/docs/reference/plugins/like/)
		'facebookLangs' => array('ru' => 'ru_RU', 'en' => 'en_US'),

		// количество объявлений на страницу
		'objectsOnPage' => 12,
		// количество результатов поиска на страницу (объявлений не касается)
		'searchOnPage' => 50,
		// тип вывода объявление
		'objectsShowType' => 1, // 1 - сплошной, 2 - постраничный
		// количество закладок на страницу (в кабинете пользователя)
		'bookmarksOnPage' => 50,

		// страница редиректа после входа
		'pageAfterLogin' => array('/site/objects/index'),
		// страница редиректа после выхода
		'pageAfterLogout' => array('/site/objects/index'),
		// страница редиректа после регистрации
		'pageAfterSocialRegistration' => array('site/objects/index'),

		// не допустимые слова для категорий первого уровня
		'forbiddenCategoryWords' => array(
			'admin',
			'object',
			'user',
			'email',
			'registration',
			'password',
		),

		// url к аве по умолчанию от корня сайта
		'avatarDefault' => '/images/avatar.png',
		'avatarSize' => 100,
		// тип граватара, если на нем не зарегестрирован пользователь
		// https://ru.gravatar.com/site/implement/images/
		'gavatarType' => 'identicon', // default (урл на аву из параметра avatarDefault)/404/mm/identicon/monsterid/wavatar/retro


		// интервал в секундах через который возможно поднятие объявления
		'objectUpTime' => 24*60*60, // сутки

		// срок жизни объявления 1 - неделя / 2 - 2 недели / 3 - месяц / 4 - 3 месяца / 5 - полгода
		'lifetime' => 3,

		// время кеширования в секундах
		// 0 - время не ограничено
		// -1 не кешировать
		'cache' => array(
			// кеширование пользователей
			'users' => 7*24*60*60, // неделя
			'newMessage' => 15*60, // 15 минут

			'news' => 30*24*60*60, // месяц

			// кеширование класса объявления \app\models\object\Object с его данными
			'object' => 7*24*60*60, // неделя
			// кеширование класса категорий \app\components\object\Category с их структурой
			'category' => 7*24*60*60, // неделя

			// кеширование наборов ManagerObject для выборок объявлений
			'objectFields' => 30*24*60*60, // месяц
			// кеширование набора полей каждого типа объявления
			'fieldsType' => 30*24*60*60, // месяц
			// кеширование класса фильтра app\components\object/Filter
			'filter' => 30*24*60*60, // месяц
			// не используется на данный момент
			// 'widgetFilter' => 30*24*60*60, // месяц

			// кеширование html кода страниц категорий (main и остальных)
			'categoryPage' => 15*60, // 15 минут
			// кеширование виджета похожие предложения
			'similarOffers' => 60*60, // час
			// кеширование виджета объявления на карте
			'objectsOnMap' => 60*60, // час

			// кеширование данных возвращаемых при ajax для значений полей
			'fieldData' => 30*24*60*60, // месяц
		),
		'mail' => array(
			'supportEmail' => 'support@geojoy.com',
			'copyright' => 'GeoJoy',
			'twitterUrl' => 'https://twitter.com/',
			'facebookUrl' => 'http://www.facebook.com/',
			'vkUrl' => 'http://vk.com/',
			'contactUrl' => 'http://geojoy.com/contacts.html',
		),
	),
);
