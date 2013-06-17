<?php
// $jquery = <<<JQUERY
// JQUERY;
	$baseUrl = Yii::app()->request->getBaseUrl();
	$cs = Yii::app()->getClientScript();
	$cs->registerCoreScript('jquery');
	$cs->registerCoreScript('cookie');
	$cs->registerScriptFile($baseUrl . '/js/site.js');
	$cs->registerScriptFile($baseUrl . '/js/bootstrap.min.js');
	// $cs->registerScript('footer', $jquery);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="">
	<meta name="robots" content="index, follow" />
	<meta name="description" content="">
	<meta name="keywords" content="" />
	<?php $this->renderHeaderMeta(); ?>
	<link href="<?php echo $baseUrl ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $baseUrl ?>/css/style.css" type="text/css" media="screen" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<div id="fb-root"></div><!-- for facebook -->
	<?php
		$flashes = Yii::app()->user->getFlashes();
		if(!empty($flashes)) {
			$cs = \Yii::app()->getClientScript();
			$cs->registerScriptFile($baseUrl . '/js/jquery.gritter.js', \CClientScript::POS_HEAD);
			$cs->registerCssFile($baseUrl . '/css/jquery.gritter.css');
			$messageShowTime = \Yii::app()->params['messageShowTime'];
			foreach($flashes as $id => $flash) {
				$jquery = <<<JQUERY
				$.gritter.add({
					// class_name: 'gritter-light',
					title: '$flash->title',
					text: '$flash->text',
					time: $messageShowTime,
				});
JQUERY;
				$cs->registerScript('flash' . $id, $jquery);
			}
		}
	?>
	<?php /* верхнее меню */ echo $this->renderPartial('app.views.layouts.menu'); ?>
	<div class="wrapper width_S categor">
		<?php /* категории объявлений */ $this->widget('\app\components\widgets\Category'); ?>
	</div>
	<?php $curLang = \Yii::app()->getLanguage(); ?>
	<div class="wrapper width_S filter_box">
		<?php /* фильтр */
			$params = \app\components\object\Filter::getInstanse()->params();
			if(empty($params)) {
				if($this->cacheBlockBegin('filter-' . $curLang, array('duration' => \Yii::app()->params['cache']['filter'], 'varyByRoute' => true))) {
					$this->widget('\app\components\widgets\Filter', array(
						'actionId' => array(
							'site/objects/index',
							'site/objects/category',
							'site/objects/indexMap',
							'site/objects/categoryMap'
						),
					));
					$this->cacheBlockEnd();
				}
			}
			else
				$this->widget('\app\components\widgets\Filter', array(
					'actionId' => array(
						'site/objects/index',
						'site/objects/category',
						'site/objects/indexMap',
						'site/objects/categoryMap'
					),
				));
		?>
	</div>

	<?php echo $content; ?>

	<?php if(\Yii::app()->getCurrentRoute() == 'site/objects/index') : ?>
		<div class="wrapper width_S tabs-on-main">
			<ul class="nav nav-tabs home">
				<li class="active"><a href="#news" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_NEWS')?></a></li>
				<li><a href="#last-objects" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_LAST_OBJETS')?></a></li>
			</ul>

			<div class="tab-content home">
				<div class="tab-pane active" id="news">
					<?php /* последние новости */
						if($this->cacheBlockBegin('news-carousel-mainPage-' . $curLang, array('duration' => \Yii::app()->params['cache']['news']))) {
							$this->widget('\app\modules\news\components\widgets\News', array(
								'count' => 8,
							));
							$this->cacheBlockEnd();
						}
					?>
				</div>
				<div class="tab-pane" id="last-objects">
					<?php /* последние объявления */
						if($this->cacheBlockBegin('lastObjects-carousel-mainPage-' . $curLang, array('duration' => \Yii::app()->params['cache']['categoryPage']))) {
							$this->widget('\app\components\widgets\LastObjects', array(
								'count' => 8,
							));
							$this->cacheBlockEnd();
						}
					?>
				</div>
			</div>
		</div>
	<?php endif ?>

	<div id="win_up"><a><?php echo \Yii::t('nav', 'NAV_UP'); ?></a></div>
	<div id="hide_footer" class="footer_up"><a><?php echo \Yii::t('nav', 'NAV_FUTER'); ?></a></div>
	<div class="footer">
	  <div class="copyright note">&copy; GeoJoy.com <?php echo date('Y'); ?></div>
	  
    
    
    <div class="footer_social">
      <a href="http://vk.com/"></a>
      <a href="http://twitter.com/GeoJoyCom"></a>
      <a href="http://facebook.com/geojoycom"></a>
      
    </div>
	</div>
</body>
</html>