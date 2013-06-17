<ul class="admin_objects">
	<?php $i = 0;
		foreach($objects as $id => $obj) :
		$i++;?>
		<li class="<?php echo ($i % 2 != 0 ? 'grey' : ''); ?>" id="object<?php echo $obj->idobjects; ?>">
			<h4 class="title"><?php echo $obj->value('title'); ?></h4>
				<div class="options">
					<?php echo CHtml::link(
						'',
						array('/site/objects/del', 'id' => $id),
						array('class' => 'delete', 'title' => Yii::t('nav', 'NAV_OBJECT_DEL'), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))
					);
					echo CHtml::link(
						'',
						array('/site/objects/edit', 'id' => $id),
						array('class' => 'edit', 'title' => Yii::t('nav', 'NAV_OBJECT_EDIT'))
					);
					echo CHtml::link(
						'',
						array('/site/objects/view', 'id' => $id),
						array('class' => 'view', 'title' => Yii::t('nav', 'NAV_OBJECT_DETAIL'), 'target' => '_blank')
					);?>
				<?php if(@$editSpam) : ?>
					<div class="moderate_spam">
						<?php echo CHtml::ajaxLink(
							'',
							array('/site/objects/toSpam', 'id' => $id),
							array('success' => 'function(html){if(html == "ok") $("#object' . $id . '").remove();}'),
							array('class' => 'sp', 'title' => Yii::t('nav', 'NAV_OBJECT_SPAM'))
						);
						echo CHtml::ajaxLink(
							'',
							array('/site/objects/notSpam', 'id' => $id),
							array('success' => 'function(html){if(html == "ok") $("#object' . $id . '").remove();}'),
							array('class' => 'nosp', 'title' => Yii::t('nav', 'NAV_OBJECT_NOT_SPAM'))
						);?>
					</div>
				<?php endif ?>
				<?php if(@$editModerate) : ?>
					<div class="moderate_spam">
						<?php echo CHtml::ajaxLink(
							'',
							array('/site/objects/moderateOk', 'id' => $id),
							array('success' => 'function(html){if(html == "ok") $("#object' . $id . '").remove();}'),
							array('class' => 'nosp', 'title' => Yii::t('nav', 'NAV_OBJECT_MODERATE_OK'))
						);?>
					</div>
				<?php endif ?>
			</div>
      <div class="object-text">
        <p><?php echo $obj->value('desc'); ?></p>
			</div>
			
				

			<div class="clear"></div>
		</li>
	<?php endforeach; ?>
	<div class="clear"></div>
</ul>
