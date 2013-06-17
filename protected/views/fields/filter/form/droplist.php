<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName()); ?>
	<?php echo $form->dropDownList($model, $model->getName(), $model->data(true), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm(), 'prompt' => \Yii::t('lists', 'ALL'))); ?>
	<?php echo $form->error($model, $model->getName()); ?>
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
		$jquery = <<<JQUERY
	$("#$idParent").change(function () {
		$.ajax({
			type: "POST",
			url: "$url",
			cache: false,
			data: {
				type: 'filter',
				field: $idField,
				needUse: 1,
				parentValue: $(this).val(),
				$csrf
			},
			success: function(html){
				$("#$id").html(html);
				if('$value' !='') {
					$("#$id").val('$value');
					$("#$id").trigger('change');
				}
			}
		});
	});
JQUERY;
		\Yii::app()->getClientScript()->registerScript("getFieldData-$name-filter", $jquery);
		$jquery = "	if($('#$idParent').val() != '') $('#$idParent').trigger('change');";
		\Yii::app()->getClientScript()->registerScript('change-' . $idParent, $jquery);
}
?>