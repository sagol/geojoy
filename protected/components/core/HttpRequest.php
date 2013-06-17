<?php

namespace app\components\core;

/**
 * Добавлены изменения для выделения языка из урла и отключения csrf валидации для заданных роутеров
 */
class HttpRequest extends \CHttpRequest {


	/**
	 * Текущий запрошеный путь
	 * @var string 
	 */
	private $_pathInfo;
	/**
	 * Отключение CSRF валидации для определенных роутеров
	 * @var array 
	 */
	public $noCsrfValidationRoutes = array();


	/**
	 * Добавлена обработка языка указанного в урле
	 * Returns the path info of the currently requested URL.
	 * This refers to the part that is after the entry script and before the question mark.
	 * The starting and ending slashes are stripped off.
	 * @return string part of the request URL that is after the entry script and before the question mark.
	 * Note, the returned pathinfo is decoded starting from 1.1.4.
	 * Prior to 1.1.4, whether it is decoded or not depends on the server configuration
	 * (in most cases it is not decoded).
	 * @throws CException if the request URI cannot be determined due to improper server configuration
	 */
	public function getPathInfo() {
		if($this->_pathInfo === null) {
			$pathInfo = $this->getRequestUri();

			/* добавлено для выделения языка из урла */
			if(!empty(\Yii::app()->params['lang'])) {
				$baseUrl = $this->getBaseUrl();

				/* удаление базового пути из урла */
				if(!empty($baseUrl) && strpos($pathInfo, $baseUrl) === 0)
					$pathInfo = substr($pathInfo, strlen($baseUrl));
				else $baseUrl = '';

				/* поиск языка в урле, если язык найден, он задается приложению */
				foreach(\Yii::app()->params['lang'] as $lang) {
					if(strpos($pathInfo, "/$lang/") === 0) {
						\Yii::app()->setLanguage($lang);
						$pathInfo = substr($pathInfo, strlen($lang)+1);
						break;
					}
				}

				/* добавление базового пути в урл. сделано что бы не менять логику от Yii */
				$pathInfo = $baseUrl . $pathInfo;
			}
			/* добавлено для выделения языка из урла */

			if(($pos=strpos($pathInfo,'?'))!==false)
			   $pathInfo=substr($pathInfo,0,$pos);

			$pathInfo=$this->decodePathInfo($pathInfo);

			$scriptUrl=$this->getScriptUrl();
			$baseUrl=$this->getBaseUrl();
			if(strpos($pathInfo,$scriptUrl)===0)
				$pathInfo=substr($pathInfo,strlen($scriptUrl));
			else if($baseUrl==='' || strpos($pathInfo,$baseUrl)===0)
				$pathInfo=substr($pathInfo,strlen($baseUrl));
			else if(strpos($_SERVER['PHP_SELF'],$scriptUrl)===0)
				$pathInfo=substr($_SERVER['PHP_SELF'],strlen($scriptUrl));
			else
				throw new CException(Yii::t('yii','CHttpRequest is unable to determine the path info of the request.'));

			$this->_pathInfo=trim($pathInfo,'/');
		}
		return $this->_pathInfo;
	}


	/**
	 * Добавлено отключение валидации для заданнх роутеров
	 * Normalizes the request data.
	 * This method strips off slashes in request data if get_magic_quotes_gpc() returns true.
	 * It also performs CSRF validation if {@link enableCsrfValidation} is true.
	 */
	protected function normalizeRequest() {
		if(!empty($this->noCsrfValidationRoutes)) {
			$route = \Yii::app()->getUrlManager()->parseUrl($this);
			if($this->enableCsrfValidation && in_array($route, $this->noCsrfValidationRoutes))
				$this->enableCsrfValidation = false;
		}

		parent::normalizeRequest();
	}


}