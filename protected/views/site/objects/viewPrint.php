<?php $this->breadcrumbs += array($model->value('title')); ?>
<div class="object">
	<?php echo $status; ?>
	<div class="clear"></div>
	<div class="tab-content">
		<div class="tab-pane active" id="object">
  		<div class="object-image thumbnail">
  			<?php /*Картинка объявления*/ echo $model->renderExt('fotos', 'main'); ?>
  		</div>
  		<div class="object-text">
			  <p  class="location"><?php /*Страна*/ $model->render('country'); ?>,
    		<?php /*Город*/ $model->render('city'); ?></p>
        <p class="price"><?php /*Цена*/ $model->render('price'); ?>
  			<span class="valuta"><?php /*Валюта*/ $model->render('valuta'); ?></span></p>
  			<p class="title"><?php $model->render('title'); ?></p>
    		<p><?php $model->render('desc'); ?></p>
  		</div>
		<div class="clear"></div>
		</div>

		<div class="tab-pane active" id="parameters">
			<?php $model->getManager()->renderGroup('LOCATION', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('PARAMS', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('OBJECT_TYPE', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('INDOOR_FACILITIES', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NOTICES', 'skipEmpty'); ?>
		</div>

		<div class="tab-pane active" id="contacts">
			<?php $user->getManager()->renderGroups('userSkipEmpty'); ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>