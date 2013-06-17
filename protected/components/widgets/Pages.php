<?php

namespace app\components\widgets;

/**
 * Виджет страниц
 */
class Pages extends \app\components\core\Widget {


	/**
	 * Html контейнер содержащий страницы
	 * @var string
	 */
	public $tagName = 'div';
	/**
	 * Html параметры
	 * @var array 
	 */
	public $htmlOptions = array('class' => 'pagination');
	/**
	 * Кодирование html
	 * @var boolean
	 */
	public $encodeLabel = true;
	/**
	 * Данные формирования страниц
	 * на пример так:
	 * array(
	 * 	'count' => 10,
	 * 	'active' => 2,
	 * 	'url' => array('/site/objects/category', 'params' => '0101'),
	 * )
	 * @var array
	 */
	public $pages = array();
	/**
	 * Разделитель страниц
	 * @var string
	 */
	public $separator = ' ... ';

	protected $_allowRoutes = array(
		'site/objects/index',
		'site/objects/category',
		'site/user/objects',
		'site/bookmarks/show',
	);

	/**
	 * Выполнение виджета
	 * @return boolean 
	 */
	public function run() {
		if(empty($this->pages)) return false;

		$paramsObjectsShowType = \Yii::app()->params['objectsShowType'];
		$currentRoute = \Yii::app()->getCurrentRoute();

		if($paramsObjectsShowType == 1 && in_array($currentRoute, $this->_allowRoutes)) $this->_renderButton();
		else $this->_renderPages();
	}


	protected function _renderButton() {
		if($this->pages['count'] > $this->pages['active']) {
			$next = $this->pages['active']+1;
			$page = $this->_pages($next, $next, 0, $this->pages['url']);
			$page[0]['title'] = \Yii::t('nav', 'NAV_CONTINUE_PAGE');

			$config['next'] = $page[0];
			$config['page'] = $next;
			$this->render('button', $config);
		}
	}


	protected function _renderPages() {
		$count = $this->pages['count'];
		$active = $this->pages['active'];
		$url = $this->pages['url'];

		if($count > 8) {
			if($active >= 5 && $active <= $count-5) {
				$p1 = $this->_pages(1, 2, $active, $url);
				$p2 = array(array('separator' => '1'));
				$p3 = $this->_pages($active-1, $active+1, $active, $url);
				$p4 = array(array('separator' => '1'));
				$p5 = $this->_pages($count-1, $count, $active, $url);
				$pages = array_merge($p1, $p2, $p3, $p4, $p5);
			}
			elseif($active < 5) {
				$p1 = $this->_pages(1, 5, $active, $url);
				$p2 = array(array('separator' => '1'));
				$p3 = $this->_pages($count-1, $count, $active, $url);
				$pages = array_merge($p1, $p2, $p3);
			}
			elseif($active > $count-5) {
				$p1 = $this->_pages(1, 2, $active, $url);
				$p2 = array(array('separator' => '1'));
				$p3 = $this->_pages($count-4, $count, $active, $url);
				$pages = array_merge($p1, $p2, $p3);
			}
		}
		else $pages = $this->_pages(1, $count, $active, $url);

		$previous = $this->_pages($active-1, $active-1, 0, $url);
		$previous[0]['title'] = 'NAV_PREVIOUS_PAGE';
		$next = $this->_pages($active+1, $active+1, 0, $url);
		$next[0]['title'] = 'NAV_NEXT_PAGE';
		if($active == $count) unset($next[0]['url']);

		$config['tagName'] = $this->tagName;
		$config['htmlOptions'] = $this->htmlOptions;
		$config['encodeLabel'] = $this->encodeLabel;
		$config['separator'] = $this->separator;
		$config['pages'] = $pages;
		$config['previous'] = $previous[0];
		$config['next'] = $next[0];

		$this->render('pages', $config);
	}


	/**
	 * Формирование массива страниц
	 * @param integer $start
	 * @param integer $end
	 * @param integer $page
	 * @param array $url
	 * @return array 
	 */
	protected function _pages($start, $end, $page, $url) {
		$pages = array();

		for($i = $start; $i <= $end; $i++) {
			if($i == $page) $pages[] = array('title' => $i);
			else $pages[] = array('title' => $i, 'url' => self::pageUrl($i, $url));
		}

		return $pages;
	}


	static function pageUrl($page, $url) {
		$filterParams = \app\components\object\Filter::getInstanse()->values();
		if(!empty($filterParams))  $url = array_merge((array)$url, $filterParams);

		if($page != 1) $url['page'] = $page;

		return $url;
	}


}
