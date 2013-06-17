<?php

namespace app\modules\news\models;

/**
 * 
 */
class News {


	static function unPackMultiLang($data) {
		$langs = \Yii::app()->params['lang'];

		foreach($langs as $lng) {
			$title[$lng] = '';
			$brief[$lng] = '';
			$news[$lng] = '';
		}

		if(isset($data['title'])) {
			$valueTitle = \app\managers\Manager::toArray($data['title']);
			foreach($langs as $lng) {
				$val = current($valueTitle);
				if($val) $title[$lng] = $val;
				next($valueTitle);
			}
			$data['title'] = $title;
		}

		if(isset($data['brief'])) {
			$valueBrief = \app\managers\Manager::toArray($data['brief']);
			foreach($langs as $lng) {
				$val = current($valueBrief);
				if($val) $brief[$lng] = $val;
				next($valueBrief);
			}
			$data['brief'] = $brief;
		}

		if(isset($data['news'])) {
			$valueNews = \app\managers\Manager::toArray($data['news']);
			foreach($langs as $lng) {
				$val = current($valueNews);
				if($val) $news[$lng] = $val;
				next($valueNews);
			}
			$data['news'] = $news;
		}


		return $data;
	}


	public function show($page, $newsOnPage) {
		$news = array();
		$sql = 'SELECT * 
			FROM news
			WHERE status = 1 AND type = 0 AND publish <= NOW() 
			ORDER BY publish DESC
			LIMIT :limit OFFSET :offset';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':offset', ($page-1)*$newsOnPage, \PDO::PARAM_INT);
		$command->bindParam(':limit', $newsOnPage, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$news[$data['idnews']] = $this->unPackMultiLang($data);


		return $news;
	}


	public function news($id) {
		$news = array();
		$sql = 'SELECT * 
			FROM news
			WHERE status = 1 AND type = 0 AND publish <= NOW() AND idnews = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':id', $id, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$news = $this->unPackMultiLang($data);


		return $news;
	}


	static function setFlashNews(&$user) {
		if(!($id = $user->getId())) return false;

		$sql = 'DELETE FROM news_show_users
			WHERE idusers = :id
			RETURNING *';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			$curLang = \Yii::app()->getLanguage();
			$count = $data['count'];
			$sql = 'SELECT title, news 
				FROM news
				WHERE idnews = :id';
			$command = \Yii::app()->db->createCommand($sql);
			$command->bindParam(':id', $data['idnews'], \PDO::PARAM_INT);
			$dataReader = $command->query();
			if(($data = $dataReader->read()) !== false) {
				$data = self::unPackMultiLang($data);
				$flash = new \stdClass;
				$flash->title = trim(json_encode($data['title'][$curLang]), '"');
				if($count > 1) $flash->text = $data['news'][$curLang] . '<p>' . 
					\CHtml::link(
						\Yii::t('app\modules\news\NewsModule.news', 'NEWS_UNWATCHED', array('{count}' => $count)),
						array('/news/show'),
						array('class' => '')
					) . 
					'</p>';
				else  $flash->text = $data['news'][$curLang];
				$flash->text = trim(json_encode($flash->text), '"');
				$user->setFlash('news', $flash);


				return true;
			}
		}


		return false;
	}


}
