<?php

namespace app\components\widgets;

/**
 * Виджет категорий сайта, выросший в тайлы
 */
class Category extends \app\components\core\Widget {


	/**
	 * Html параметры
	 * @var array 
	 */
	public $htmlOptions = array('class' => 'category');
	/**
	 * Кодирование html
	 * @var boolean 
	 */
	public $encodeLabel = true;
	/**
	 * Категории
	 * @var array 
	 */
	protected static $_categories = array();


	/**
	 * Выполнение виджета
	 */
	public function run() {
		if(empty(self::$_categories)) {
			$category = \app\components\object\Category::getInstanse();
			$code = $category->code();
			$data = $category->data();
			$filterParams = \app\components\object\Filter::getInstanse()->params();
			foreach($code as $path => $id) {
				if(strlen($data[$id]['tree']) > 2 ) continue;

				if(empty($filterParams)) $url = array('/site/objects/category', 'params' => $data[$id]['tree'], 'useMap' => $category->getUseMap());
				else $url = array_merge(array('/site/objects/category', 'params' => $data[$id]['tree'], 'useMap' => $category->getUseMap()), $filterParams);

				self::$_categories[$id] = array(
					'alias' => $data[$id]['alias'],
					'label' => $data[$id]['name'],
					'url' => $url,
					'img' => $data[$id]['img'],
				);
			}
			unset($category, $code, $data);
		}

		$active = '';
		$categoryTree = $this->getOwner()->categoryTree;
		foreach(self::$_categories as $id => $value)
			if($value['url']['params'] == $categoryTree) $active = $id;

		$config['htmlOptions'] = $this->htmlOptions;
		$config['encodeLabel'] = $this->encodeLabel;
		$config['categories'] = self::$_categories;
		$config['active'] = $active;
		$config['count'] = count(self::$_categories);

		$this->render('category', $config);
	}


}