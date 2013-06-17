<?php

namespace app\components\core;

/**
 * Добавлены изменения для поддержки языка, проверки суффикса, задания страницы ошибки админки
 */
class UrlManager extends \CUrlManager {


	/**
	 * Добавление текущего языка сайта в адрес
	 * Язык по умолчанию не добавляется
	 * 
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
	 * If the name is '#', the corresponding value will be treated as an anchor
	 * and will be appended at the end of the URL.
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 */
	public function createUrl($route, $params = array(), $ampersand = '&') {

		if(!empty($params['lang'])) {
			$lang = $params['lang'];
			unset($params['lang']);
		}
		else $lang = \Yii::app()->getLanguage();

		if(\Yii::app()->getDefaultLanguage() != $lang) { // язык по умолчанию не добавляется
			$lang = '/' . $lang;
			$baseUrl = $this->getBaseUrl();
			if($baseUrl) {
				$this->setBaseUrl("$baseUrl$lang");
				$uri = parent::createUrl($route, $params, $ampersand);
				$this->setBaseUrl($baseUrl);
			}
			else {
				$uri = parent::createUrl($route, $params, $ampersand);
				$uri = $lang . $uri;
			}

			if($uri == '' && $this->urlSuffix) $uri .= '/';


			return $uri;
		}
		else return parent::createUrl($route, $params, $ampersand);
	}


	/**
	 * Проверка наличия суффикса в урле и задание страницы ошибки для админки
	 * 
	 * Parses the user request.
	 * @param CHttpRequest $request the request application component
	 * @return string the route (controllerID/actionID) and perhaps GET parameters in path format.
	 * @throws \CHttpException если задано использование суффикса в урле и его нету
	 */
	public function parseUrl($request) {
		// вывод исключения при отсутствии суфикса в адресе
		if($this->urlSuffix) {
			$rawPathInfo = $request->getPathInfo();
			$f = strpos($rawPathInfo, $this->urlSuffix);
			if(!$f && $rawPathInfo != '') throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		}

		// задание страницы ошибки для админки
		if(strpos($request->getPathInfo(), 'admin/') === 0)
			\Yii::app()->errorHandler->errorAction = 'admin/admin/error';


		return parent::parseUrl($request);
	}


}