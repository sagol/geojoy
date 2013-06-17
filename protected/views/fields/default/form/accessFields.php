<div class="control-group">
<?php echo $form->dropDownList($model, $model->getName() . "[$fieldName]", \app\models\users\User::getAccessFields(), array(
	'class' => '',
	'id' => $model->getName() . "_$fieldName",
	'name' => $model->getNameInForm() . "[$fieldName]",
)); ?>
<?php echo $form->error($model, $model->getName() . "[$fieldName]", array('inputID' => $model->getName() . "_$fieldName")); ?>
</div>