<?php $curLang = \Yii::app()->getLanguage(); ?>
<?php $this->breadcrumbs += array(Yii::t('nav', 'NAV_NEWS') => array('/news/show'), $news['title'][$curLang]); ?>
<div class="news">
	<h3><?php echo $news['title'][$curLang];?></h3>
	<p class="note"><?php echo \Yii::app()->dateFormatter->formatDateTime($news['create'], 'medium', null); ?></p>
	<p><?php echo $news['news'][$curLang];?></p>
</div>