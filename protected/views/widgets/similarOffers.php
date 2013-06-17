<?php
	$count = count($objects);
	if($count <= 4) $hideButtons = 'if(carousel.options.scroll >= ' . $count . ') {$(".jcarousel-prev").hide();$(".jcarousel-next").hide();} else {$(".jcarousel-prev").show();$(".jcarousel-next").show();}';
	else $hideButtons = '';

	$jcarousel = <<<JQUERY
	$('.jcarousel').jcarousel({
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
	$cs->registerScript('jcarousel', $jcarousel, \CClientScript::POS_READY);
?>
<h4><?php echo Yii::t('nav', 'SIMILAR_OBJECTS'); ?></h4>
<div class="similar-offers jcarousel">
	<ul class="objects">
		<?php foreach($objects as $id => $obj) : ?>
			<li id="object<?php echo $obj->idobjects; ?>">
				<div class="object-image thumbnail">
					<?php /*Картинка объявления*/ echo CHtml::link($obj->renderExt('fotos', 'main', 'default', 'html', true), array('/site/objects/view', 'id' => $id)/*, array('target' => '_blank')*/); ?>
					<div class="up undraw">
						<p><?php echo $obj->render('country'); ?>
						<?php echo $obj->render('city'); ?></p>
						<p class="price"><?php echo $obj->render('price'); ?>
						<?php echo $obj->render('valuta'); ?></p>
						<p class="hide"><?php echo $obj->render('desc'); ?></p>
						<div class="img-up"></div>
					</div>
				</div>
				<div class="clear"></div>
			</li>
		<?php endforeach; ?>
		<div class="clear"></div>
	</ul>
</div>