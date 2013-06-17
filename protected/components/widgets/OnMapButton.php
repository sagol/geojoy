<?php

namespace app\components\widgets;

/**
 * Виджет кнопки вывода объявлений на карте
 */
class OnMapButton extends \app\components\core\Widget {


	protected $_allowRoute = array(
		'site/objects/index',
		'site/objects/category',
		'site/objects/indexMap',
		'site/objects/categoryMap',
	);


	/**
	 * Выполнение виджета
	 */
	public function run() {
		$currentRoute = \Yii::app()->getCurrentRoute();
		if(!in_array($currentRoute, $this->_allowRoute)) return false;

		if($currentRoute == 'site/objects/index')
			$this->render('onMapButton', array('router' => array('site/objects/indexMap')));
		elseif($currentRoute == 'site/objects/indexMap')
			$this->render('onObjectButton', array('router' => array('site/objects/index')));
		elseif($currentRoute == 'site/objects/category') {
			$curCategory = \app\components\object\Category::getInstanse()->getCurCategory();
			$this->render('onMapButton', array('router' => array('site/objects/categoryMap', 'params' => $curCategory)));
		}
		elseif($currentRoute == 'site/objects/categoryMap'){
			$curCategory = \app\components\object\Category::getInstanse()->getCurCategory();
			$this->render('onObjectButton', array('router' => array('site/objects/category', 'params' => $curCategory)));
		}


	}


}