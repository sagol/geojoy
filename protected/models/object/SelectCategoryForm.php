<?php

namespace app\models\object;

/**
 * Выбор категории
 */
class SelectCategoryForm extends \CFormModel {


	/**
	 * Категории
	 * @var array 
	 */
	private static $_category = array();
	/**
	 * Отключенные категории
	 * @var array 
	 */
	private static $_categoryDisabled = array();
	/**
	 * Свойство модели
	 * @var integer 
	 */
	public $category;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('category', 'required'),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'category' => \Yii::t('nav', 'FORM_SELECT_CATEGORY_CATEGORY'),
		);
	}


	/**
	 * Категории
	 * @return array 
	 */
	public function category() {
		if(empty(self::$_category)) {
			$category = \app\components\object\Category::getInstanse();
			$code = $category->code();
			$data = $category->data();

			unset($code['main']);
			foreach($code as $id)
				self::$_category[$id] =  str_pad('', (strlen($id)-2)*2, '-', STR_PAD_LEFT) . \Yii::t('lists', $data[$id]['name']);

			unset($category, $code, $data);
		}


		return self::$_category;
	}


	/**
	 * Отключенные категории
	 * @return array 
	 */
	public function categoryDisabled() {
		if(empty(self::$_categoryDisabled)) {
			$category = \app\components\object\Category::getInstanse();

			foreach($category->data() as $id => $value)
				if(!$value['type']) self::$_categoryDisabled[$id] = array('disabled' => 'disabled');

			unset($category);
		}


		return self::$_categoryDisabled;
	}


}