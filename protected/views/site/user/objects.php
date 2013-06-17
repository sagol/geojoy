<?php /*табы*/ if(empty($notShowTabs)) $this->renderPartial('app.views.layouts.userTabs'); ?>
<ul class="objects">
	<?php $curDate = new \DateTime(); ?>
	<?php foreach($objects as $id => $obj) : ?>
		<li id="object<?php echo $obj->idobjects; ?>">
			<div class="object-image thumbnail">
				<div class="status">
					<?php
						if($obj->moderate == \app\models\object\Object::OBJECT_MODERATE_NEED)
							echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_MODERATE');
						elseif($obj->spam == \app\models\object\Object::OBJECT_SPAM_EXACTLY)
							echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_SPAM');
						elseif($obj->categoryDisabled)
							echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_CATEGORY_DISABLED');
						elseif($obj->getManager()->hasField(\app\models\object\Object::FIELD_LIFETIME)) {
							$date = new \DateTime($obj->field(\app\models\object\Object::FIELD_LIFETIME)->getValueDate());
							$interval = $date->diff($curDate);
							if(!$interval->invert) echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_LIFETIME');
						}
					?>
				</div>
				<a class="" href="<?php echo $this->createUrl('/site/objects/view', array('id' => $id)); ?>">
					<?php /*Картинка объявления*/ $obj->renderExt('fotos', 'main'); ?>
				</a>
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