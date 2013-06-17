<?php

namespace app\components\core;

/**
 * Добавлены измененя для загрузки контроллеров с namespace, и сохранения языка сайта по умолчанию
 */
class WebApplication extends \CWebApplication {


	/**
	 * Язык сайта по умолчанию
	 * @var string 
	 */
	protected $_defaultLanguage;
	protected $_currentRoute;

	/**
	 * Сохраняется язык сайта по умолчанию
	 */
	protected function init() {
		$this->_defaultLanguage = \Yii::app()->getLanguage();
		parent::init();
	}


	/**
	 * Возвращает языка сайта по умолчанию
	 * @return string 
	 */
	public function getDefaultLanguage() {
		return $this->_defaultLanguage;
	}


	public function getCurrentRoute() {
		return $this->_currentRoute;
	}


	public function runController($route) {
		$this->_currentRoute = $route;

		parent::runController($route);
	}


	/**
	 * Добалены измененя для поддержки контроллеров с namespace
	 * 
	 * Creates a controller instance based on a route.
	 * The route should contain the controller ID and the action ID.
	 * It may also contain additional GET variables. All these must be concatenated together with slashes.
	 *
	 * This method will attempt to create a controller in the following order:
	 * <ol>
	 * <li>If the first segment is found in {@link controllerMap}, the corresponding
	 * controller configuration will be used to create the controller;</li>
	 * <li>If the first segment is found to be a module ID, the corresponding module
	 * will be used to create the controller;</li>
	 * <li>Otherwise, it will search under the {@link controllerPath} to create
	 * the corresponding controller. For example, if the route is "admin/user/create",
	 * then the controller will be created using the class file "protected/controllers/admin/UserController.php".</li>
	 * </ol>
	 * @param string $route the route of the request.
	 * @param CWebModule $owner the module that the new controller will belong to. Defaults to null, meaning the application
	 * instance is the owner.
	 * @return array the controller instance and the action ID. Null if the controller class does not exist or the route is invalid.
	 */
	public function createController($route, $owner = null) {
		if($owner === null)
			$owner = $this;
		if(($route = trim($route, '/')) === '')
			$route = $owner->defaultController;
		$caseSensitive = $this->getUrlManager()->caseSensitive;

		$route .= '/';
		while(($pos = strpos($route, '/')) !== false)
		{
			$id = substr($route, 0, $pos);
			if(!preg_match('/^\w+$/', $id))
				return null;
			if(!$caseSensitive)
				$id = strtolower($id);
			$route = (string)substr($route, $pos+1);

			if(!isset($basePath))  // first segment
			{
				if(isset($owner->controllerMap[$id]))
				{
					return array(
						\Yii::createComponent($owner->controllerMap[$id], $id, $owner === $this?null:$owner),
						$this->parseActionParams($route),
					);
				}

				if(($module = $owner->getModule($id)) !== null)
					return $this->createController($route, $module);

				$basePath = $owner->getControllerPath();
				$controllerID = '';
			}
			else 
				$controllerID .= '/';
			$className = ucfirst($id) . 'Controller';
			$classFile = $basePath . DIRECTORY_SEPARATOR . $className . '.php';

			if(is_file($classFile))
			{
				if(!class_exists($className, false))
					require($classFile);

				// добавлено для поддержки namespace
				if(!class_exists($className, false)) {
					$tmpClassName = 'app\controllers\\' . str_replace('/', '\\', $controllerID) . $className;
					if(class_exists($tmpClassName, false)) $className = $tmpClassName;
					else {
						$tmpClassName = 'app\modules\\' . $owner->id . '\controllers\\' . str_replace('/', '\\', $controllerID) . $className;
						if(class_exists($tmpClassName, false)) $className = $tmpClassName;
					}
				}
				// добавлено для поддержки namespace

				if(class_exists($className, false) && is_subclass_of($className, 'CController'))
				{
					$id[0] = strtolower($id[0]);
					return array(
						new $className($controllerID . $id, $owner === $this ? null : $owner),
						$this->parseActionParams($route),
					);
				}
				return null;
			}
			$controllerID .= $id;
			$basePath .= DIRECTORY_SEPARATOR . $id;
		}
	}


}