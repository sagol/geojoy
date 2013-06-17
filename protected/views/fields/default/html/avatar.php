<?php
echo '<img src="' . $value . '">' . ($model->units ? ' ' . \Yii::t('lists', $model->units) : '');