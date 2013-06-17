<?php
if(empty($value) && $model->getNoImg()) $value = $model->getNoImg();
?>
<img  src="<?php echo $value; ?>" />