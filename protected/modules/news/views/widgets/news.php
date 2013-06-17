<?php
	$count = count($news);
	if($count <= 4) $hideButtons = 'if(carousel.options.scroll >= ' . $count . ') {$(".news .jcarousel-prev").hide();$(".news .jcarousel-next").hide();} else {$(".news .jcarousel-prev").show();$(".news .jcarousel-next").show();}';
	else $hideButtons = '';

	$jcarousel = <<<JQUERY
	$('.news.jcarousel').jcarousel({
		'reloadCallback': function (carousel){
			var first = carousel.first-1;
			if(first < 0) first = 0;
			var width = carousel.list.children('li:first').width();
			carousel.list.css('left', -first*width);

			if($('.width_S').length) carousel.options.scroll = 2;
			else if($('.width_M').length) carousel.options.scroll = 3;
			else carousel.options.scroll = 4;

			$hideButtons
		},
		'initCallback': function (carousel, action){
			if($('.width_S').length) carousel.options.scroll = 2;
			else if($('.width_M').length) carousel.options.scroll = 3;
			else carousel.options.scroll = 4;

			$hideButtons
		},
		// auto: 10,
	});
JQUERY;

	$cs = \Yii::app()->getClientScript();
	$cs->registerScriptFile(Yii::app()->request->getBaseUrl() . '/js/jquery.jcarousel.js', \CClientScript::POS_HEAD);
	$cs->registerScript('jcarousel-news', $jcarousel, \CClientScript::POS_READY);

	$curLang = \Yii::app()->getLanguage();
?>
<div class="news jcarousel">
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
</div>