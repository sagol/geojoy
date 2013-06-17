<?php

namespace app\components\widgets;

\Yii::import('zii.widgets.CMenu');

/**
 * Виджет меню, добавлен перевод названий меню
 */
class Menu extends \CMenu {


	/**
	 * Массив названий словарей перевода или строка с названием одного словаря
	 * @var mixed 
	 */
	public $translate = null;


	/**
	 * Вывод элемента меню
	 * @param array $item
	 * @return string 
	 */
	protected function renderMenuItem($item) {
		if(is_array($this->translate)) {
			foreach($this->translate as $translate) {
				$labelTranslate = \Yii::t($translate, $item['label']);
				if($labelTranslate != $item['label']) break;
			}
		}
		else $labelTranslate = \Yii::t($this->translate, $item['label']);


		if(isset($item['url']))
			return \CHtml::link($labelTranslate, $item['url'], isset($item['linkOptions']) ? $item['linkOptions'] : array());
		else
			return \CHtml::tag('span', isset($item['linkOptions']) ? $item['linkOptions'] : array(), $labelTranslate);
	}


}