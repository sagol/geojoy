<div class="filter">
	<div class="spoiler_title"><?php echo \Yii::t('nav', 'FILTER'); ?><div class="undraw"></div></div>
	<div class="spoiler_body">
	<?php
		$filterForm = $this->beginWidget('CActiveForm', array(
			'method' => 'get',
			'id' => 'filter-form',
			'action' => $action,
		));
		echo '<input type="hidden" name="set" value="1">';
		$model->getManager()->setForm($filterForm);
		foreach($this->getManager()->fields() as $name => $field)
			$this->getManager()->render($name, 'filter', 'form');
	?>
		<div class="clear"></div>
		<div class="control-group">
			<?php echo CHtml::link(
				Yii::t('nav', 'FILTER_BUTTON_SHOW'),
				$action,
				array('class' => 'btn btn-primary', 'onclick' => '$("#filter-form").submit(); return false')
			);?>
		</div>
	<?php $this->endWidget(); ?>
	</div>
	<div class="clear"></div>
</div>