<?php

namespace app\components;

/**
 * Базовый контроллер для всех контроллеров фротенда
 */
class Controller extends \CController {


	/**
	 * Хлебные крошки
	 * @var array
	 */
	private static $_breadcrumbs = array();
	/**
	 * Html код мета тегов добавляемых в хедер
	 * @var array 
	 */
	private static $_headerMeta = array();

	/**
	 * Макет
	 * @var string 
	 */
	public $layout = '//layouts/siteContainer';
	/**
	 * Меню
	 * @var array 
	 */
	public $menu = array();
	/**
	 * Левое меню
	 * @var array 
	 */
	public $leftMenu = array();
	/**
	 * Страницы
	 * @var array 
	 */
	public $pages = array();
	/**
	 * Путь категории
	 * @var string 
	 */
	public $categoryPath = null;
	// используется в фильтре
	/**
	 * Код категории
	 * @var string 
	 */
	public $categoryTree = null;


	/**
	 * Устанавливает хлебные крошки
	 * @param array $breadcrumbs 
	 */
	public function setBreadcrumbs($breadcrumbs = array()) {
		self::$_breadcrumbs = (array)$breadcrumbs;
	}


	/**
	 * Возвращает массив хлебных крошек
	 * @return array 
	 */
	public function getBreadcrumbs() {
		return self::$_breadcrumbs;
	}


	/**
	 * Устанавливает мета теги хедера
	 * @param array $headerMeta 
	 */
	public function setHeaderMeta($headerMeta = array()) {
		self::$_headerMeta = (array)$headerMeta;
	}


	/**
	 * Возвращает мета теги хедера
	 * @return array 
	 */
	public function getHeaderMeta() {
		return self::$_headerMeta;
	}


	/**
	 * Выводит мета теги хедера
	 * @param boolean $return
	 * @return boolean 
	 */
	public function renderHeaderMeta($return = false) {
		if(empty(self::$_headerMeta)) return true;

		if($return) return implode("\n", self::$_headerMeta);

		echo implode("\n", self::$_headerMeta);
	}


	/**
	 * Аналог COutputCache c изменениями и улучшениями
	 * varyByRoute = false по умолчанию
	 * при duration = 0 задается неограниченый срок кеша без отключений
	 */
	public function cacheBlockBegin($id, $properties = array()) {
		$properties['id'] = $id;
		$cache = $this->beginWidget('\app\components\widgets\CacheBlock', $properties);
		if($cache->getIsContentCached()) {
			$this->cacheBlockEnd();
			return false;
		}
		else return true;
	}


	/**
	 * Аналог COutputCache
	 * varyByRoute = false по умолчанию
	 * при duration = 0 задается неограниченый срок кеша без отключений
	 */
	public function cacheBlockEnd() {
		$this->endWidget('\app\components\widgets\CacheBlock');
	}


	/**
	 * @param string $value the page title.
	 */
	public function setPageTitle($value) {
		parent::setPageTitle(\Yii::app()->name . ' - '. $value);
	}


}
