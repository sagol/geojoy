<?php
echo '<ul>';
foreach($value as $val)
	echo '<li>' . $val  . ($model->units ? ' ' . \Yii::t('lists', $model->units) : '') . '</li>';

echo '</ul>';

