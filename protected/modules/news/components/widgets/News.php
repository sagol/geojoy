<?php

namespace app\modules\news\components\widgets;

/**
 * Виджет последних новостей
 */
class News extends \app\components\core\Widget {


	/**
	 * Количество выводимых новостей
	 */
	public $count = 8;


	/**
	 * Выполнение виджета
	 */
	public function run() {
		$model = new \app\modules\news\models\News;
		$news = $model->show(1, $this->count);


		$this->render('news', array(
			'news' => $news,
		));
	}


}