<?php

namespace app\components;

/**
 * Урл правила для категорий
 */
class CategoryUrlRule extends \CBaseUrlRule {


	/**
	 * Создание урла
	 * @param CUrlManager $manager
	 * @param string $route
	 * @param array $params
	 * @param string $ampersand
	 * @return string|boolean 
	 */
	public function createUrl($manager, $route, $params, $ampersand) {
		if($route == 'site/objects/index') {
			$return = array();

			if(isset($params['useMap']) && $params['useMap']) $return[] = 'map';
			if(!empty($params['page']) && $params['page'] > 1) $return[] = $params['page'];
			if(!empty($return)) $return = implode('/', $return) . $manager->urlSuffix;
			else $return = '';
			unset($params['params'], $params['page'], $params['useMap']);

			if(!empty($params)) $return .= '?' . $manager->createPathInfo($params, '=', $ampersand);


			return $return;
		}

		if($route == 'site/objects/indexMap') {
			$return = 'map' . $manager->urlSuffix;
			unset($params['params'], $params['page'], $params['useMap']);
			if(!empty($params)) $return .= '?' . $manager->createPathInfo($params, '=', $ampersand);


			return $return;
		}

		if($route == 'site/objects/category') {
			$return = array();
			if(isset($params['useMap']) && $params['useMap']) $return[] = 'map';
			if(!empty($params['params'])) $return[] = @\app\components\object\Category::getInstanse()->data($params['params'], 'path');
			if(!empty($params['page']) && $params['page'] > 1) $return[] = $params['page'];
			$return = implode('/', $return) . $manager->urlSuffix;
			unset($params['params'], $params['page'], $params['useMap']);

			if(!empty($params)) $return .= '?' . $manager->createPathInfo($params, '=', $ampersand);


			return $return;
		}

		if($route == 'site/objects/categoryMap') {
			$return = 'map/' . @\app\components\object\Category::getInstanse()->data($params['params'], 'path');
			$return .= $manager->urlSuffix;
			unset($params['params'], $params['page'], $params['useMap']);
			if(!empty($params)) $return .= '?' . $manager->createPathInfo($params, '=', $ampersand);


			return $return;
		}

		if($route == 'site/objects/add') {
			$return = array();
			if(!empty($params['params'])) $return[] = @\app\components\object\Category::getInstanse()->data($params['params'], 'path');
			$return[] = 'add';
			$return = implode('/', $return) . $manager->urlSuffix;
			unset($params['params'], $params['page'], $params['useMap']);

			if(!empty($params)) $return .= '?' . $manager->createPathInfo($params, '=', $ampersand);


			return $return;
		}


		return false;  // не применяем данное правило
	}
 

	/**
	 * Парсинг урла
	 * @param CUrlManager $manager
	 * @param CHttpRequest $request
	 * @param string $pathInfo
	 * @param string $rawPathInfo
	 * @return string|boolean 
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo) {
		if(strlen($pathInfo) == 3 && $pathInfo == 'map') {
			$map = 'Map';
			$pathInfo = '';
			\app\components\object\Category::getInstanse()->setUseMap(true);
		}
		elseif(substr($pathInfo, 0, 4) == 'map/') {
			$map = 'Map';
			$pathInfo = substr($pathInfo, 4);
			\app\components\object\Category::getInstanse()->setUseMap(true);
		}
		else $map = '';

		if($pathInfo == '') {
			$_GET['categoryTree'] = '';
			\app\components\object\Category::getInstanse()->setCurCategory('main');

			return 'site/objects/index' . $map;
		}
		
		if(is_numeric($pathInfo)) {
			$_GET['categoryTree'] = '';
			$_GET['page'] = $pathInfo;
			\app\components\object\Category::getInstanse()->setCurCategory('main');

			return 'site/objects/index' . $map;
		}

		$category = \app\components\object\Category::getInstanse()->code();
		if(($tree = @$category[$pathInfo])) {
			$_GET['categoryTree'] = $tree;
			\app\components\object\Category::getInstanse()->setCurCategory($tree);

			return 'site/objects/category' . $map;
		}
		elseif(substr($pathInfo, -4) == '/add' && ($tree = @$category[substr($pathInfo, 0, -4)])) {
			$_GET['categoryTree'] = $tree;
			\app\components\object\Category::getInstanse()->setCurCategory($tree);

			return 'site/objects/add';
		}

		$f = strrpos($pathInfo, '/');
		$page = substr($pathInfo, $f+1);

		$pathInfo = substr($pathInfo, 0, $f);
		if(($tree = @$category[$pathInfo]) && is_numeric($page)) {
			$_GET['categoryTree'] = $tree;
			$_GET['page'] = $page;
			\app\components\object\Category::getInstanse()->setCurCategory($tree);

			return 'site/objects/category' . $map;
		}


		return false;  // не применяем данное правило
	}


}