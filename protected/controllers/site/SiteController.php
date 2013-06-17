<?php

namespace app\controllers\site;

/**
 * Внешний вид не связанный с объявлениями
 */
class SiteController extends \app\components\Controller {


	/**
	 * Вывод страницы ошибки
	 */
	public function actionError() {
	    if($error = \Yii::app()->errorHandler->error) {
	    	if(\Yii::app()->request->isAjaxRequest) echo $error['message'];
	    	else $this->render('error', $error);
	    }
	}


	public function actionSearch($search = null, $page = 1) {
		$this->breadcrumbs += array(\Yii::t('nav', 'SEARCH'));
		$model = new \app\models\search\Search;

		if(empty($search)) {
			$this->render('search', array(
				'search' => $search,
			));
			return;
		}
		elseif(is_numeric($search)) {
			$results = $model->searchObjects((int)$search, $page, $this);
			if(!empty($results)) {
				$this->render('searchObjects', array(
					'objects' => $results,
					'search' => $search,
				));
				return;
			}
		}
		else {
			$results = $model->searchUser($search, $page, $this);
			if(!empty($results)) {
				$this->render('searchUser', array(
					'users' => $results,
					'search' => $search,
				));
				return;
			}
		}


		$this->render('searchNo', array(
			'search' => $search,
		));
	}


	public function actionPages($article) {
		$file = $this->getViewPath() . DS . 'pages' . DS . $article . '.php';
		$file = \Yii::app()->findLocalizedFile($file);
		$html = $this->renderFile($file, null, true);
		$this->renderText($html);
	}


}
