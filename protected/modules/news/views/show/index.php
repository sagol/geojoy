<?php $this->breadcrumbs += array(Yii::t('nav', 'NAV_NEWS')); ?>
<?php $curLang = \Yii::app()->getLanguage(); ?>
<ul class="news">
	<?php foreach($news as $nw) : ?>
		<li>
			<h3>
				<?php echo CHtml::link(
					$nw['title'][$curLang],
					array('/news/show/news', 'id' => $nw['idnews'])
				);?>
			</h3>
			<p class="note"><?php echo \Yii::app()->dateFormatter->formatDateTime($nw['create'], 'medium', null); ?></p>
			<p><?php echo $nw['brief'][$curLang];?></p>
		</li>
	<?php endforeach ?>
</ul>