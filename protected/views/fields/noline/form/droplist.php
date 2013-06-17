<div class="control-group noline">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->dropDownList($model, $model->getName(), $model->data(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm(), 'prompt' => \Yii::t('fields', 'NOT_SELECT'))); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>
<?php
if($model->getParent()) {
		$url = \Yii::app()->createUrl('/site/objects/getFieldData');
		if(\Yii::app()->request->enableCsrfValidation) {
			$csrfTokenName = \Yii::app()->request->csrfTokenName;
			$csrfToken = \Yii::app()->request->csrfToken;
			$csrf = "'$csrfTokenName':'$csrfToken',";
		}
		else $csrf = '';

		$name = $model->getName();

		$idParent = \CHtml::getIdByName($model->getParentName());
		$idField = $model->getId();
		$id = \CHtml::getIdByName($model->getName());
		$value = $model->getValue();
		if(isset($value)) $value = "$('#$id').val('$value');";
		$jquery = <<<JQUERY
	$("#$idParent").change(function () {
		$.ajax({
			type: "POST",
			url: "$url",
			cache: false,
			data: {
				type: 'default',
				field: $idField,
				parentValue: $(this).val(),
				$csrf
			},
			success: function(html){
				$("#$id").html(html);
				$value
				$("#$id").trigger('change');
			}
		});
	});
JQUERY;
		\Yii::app()->getClientScript()->registerScript("getFieldData-$name-default", $jquery);
		$jquery = "	if($('#$idParent').val() != '') $('#$idParent').trigger('change');";
		\Yii::app()->getClientScript()->registerScript('change-' . $idParent, $jquery);
}
?>