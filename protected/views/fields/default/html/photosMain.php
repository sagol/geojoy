<?php
$value = $model->mainPhoto();
if(empty($value)) $value = Yii::app()->request->getBaseUrl() . '/images/img.png';
?>
<img  src="<?php echo $value; ?>" />