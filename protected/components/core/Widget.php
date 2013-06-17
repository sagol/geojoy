<?php

namespace app\components\core;

/**
 * Добалены изменения для задания базового пути видов виджетов в views/widgets
 */
class Widget extends \CWidget {


	/**
	 * @var array view paths for different types of widgets
	 */
	private static $_viewPaths;


	/**
	 * Замена функции CWidget, виды храняться в views/widgets и без папок при переданом $dir = false
	 * Возвращает путь к виду
	 * 
	 * @param boolean $checkTheme whether to check if the theme contains a view path for the widget.
	 * @param boolean $dir при true, возвращает путь с папкой виджета
	 * @return string 
	 */
	public function getViewPath($checkTheme = false, $dir = false) {
		$className = get_class($this);

		if(isset(self::$_viewPaths[$className])) return self::$_viewPaths[$className];
		else {
			if($checkTheme && ($theme = \Yii::app()->getTheme()) !== null) {
				$path = $theme->getViewPath() . DS;
				if($dir) {
					$path .= DS;
					if(strpos($className,'\\') !== false) // namespaced class
						$path .= str_replace('\\', '_', ltrim($className, '\\'));
					else $path .= $className;
				}


				if(is_dir($path)) return self::$_viewPaths[$className] = $path;
			}

			if($dir) {
				if($f = strrpos($className, '\\')) $dir = substr($className, $f+1);
				else $dir = $className;

				$dir = DS .  strtolower($dir);
			}

			$module = \Yii::getPathOfAlias('app') . DS . 'modules' . DS;
			$class = new \ReflectionClass($this);
			$path = $class->getFileName();
			$f = strpos($path, $module);
			if($f === false) 
				$path = \Yii::app()->getViewPath() . DS .  'widgets' . $dir;
			else {
				$f = strlen($module);
				$f1 = strpos($path, DS, $f+1);
				$path = $module . substr($path, $f, $f1-$f) . DS . 'views' . DS . 'widgets' . $dir;
			}

			if(is_dir($path)) return self::$_viewPaths[$className] = $path;
		}
	}


}