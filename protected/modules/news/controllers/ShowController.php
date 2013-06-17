<?php

namespace app\modules\news\controllers;

/**
 * 
 */
class ShowController extends \app\components\Controller {


	/**
	 * 
	 */
	public function actionIndex($page = 1) {
		$model = new \app\modules\news\models\News;
		$news = $model->show($page, $this->getModule()->newsOnPage);

		if(empty($news)) $this->render('indexNo', array('news' => $news));
		else $this->render('index', array('news' => $news));
	}


	public function actionNews($id) {
		$model = new \app\modules\news\models\News;
		$news = $model->news($id);

		if(empty($news)) throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		else $this->render('news', array('news' => $news));
	}


}