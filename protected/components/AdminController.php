<?php

namespace app\components;

/**
 * Базовый контроллер для всех контроллеров бекенда
 */
class AdminController extends \CController {

	/**
	 * Макет
	 * @var string 
	 */
	public $layout = '//layouts/adminContainer';
	/**
	 * Меню
	 * @var array 
	 */
	public $menu = array();
	/**
	 * Хлебные крошки
	 * @var array 
	 */
	private static $_breadcrumbs = array();
	/**
	 * Страницы
	 * @var array 
	 */
	public $pages = array();
	/**
	 * Главное меню
	 * @var array 
	 */
	public $mainMenu = array();
	/**
	 * Название меню
	 * @var string 
	 */
	public $menuTitle;


	/**
	 * Устанавливает хлебные крошки
	 * @param array $breadcrumbs 
	 */
	public function setBreadcrumbs($breadcrumbs = array()) {
		self::$_breadcrumbs = $breadcrumbs;
	}


	/**
	 * Возвращает массив хлебных крошек
	 * @return array 
	 */
	public function getBreadcrumbs() {
		return self::$_breadcrumbs;
	}


	/**
	 * Инициализация
	 */
	public function init() {
		self::$_breadcrumbs = array(\Yii::t('admin', 'BREADCRUMBS_ADMINS') => array('admin/admin'));
		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL');

		$accessModer = \Yii::app()->user->checkAccess('moder');
		$accessAdmin = \Yii::app()->user->checkAccess('admin');
		$this->mainMenu = array(
			array('label' => \Yii::t('admin', 'MENU_NEWS'), 'url' => array('admin/news'), 'visible' => $accessModer),
			array('label' => \Yii::t('admin', 'MENU_OBJECTS'), 'url' => array('admin/object/objects'), 'visible' => $accessModer),
			array('label' => \Yii::t('admin', 'MENU_USERS'), 'url' => array('admin/users/users'), 'visible' => $accessModer),
			array('label' => \Yii::t('admin', 'MENU_KARMA'), 'url' => array('admin/users/karma'), 'visible' => $accessModer),
			array('label' => \Yii::t('admin', 'MENU_LOGS'), 'url' => array('admin/logs'), 'visible' => $accessModer),
			array('label' => \Yii::t('admin', 'MENU_MESSAGES'), 'url' => array('admin/messages'), 'visible' => $accessModer),
			// array('label' => '/', 'itemOptions' => array('class' => 'divider'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_OBJECTS_TYPES'), 'url' => array('admin/object/type'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_OBJECTS_CATEGORY'), 'url' => array('admin/object/category'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_OBJECTS_FIELDS'), 'url' => array('admin/object/fields'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_FIELDS_VALUES'), 'url' => array('admin/object/fieldsValues'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_TIES_GROUPS'), 'url' => array('admin/object/tiesGroups'), 'visible' => $accessAdmin),
			array('label' => \Yii::t('admin', 'MENU_TIES'), 'url' => array('admin/object/ties'), 'visible' => $accessAdmin),
		);
	}


}
