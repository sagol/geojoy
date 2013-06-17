<option value=""><?php echo Yii::t('nav', 'NOT_SELECT'); ?></option>
<?php foreach($options as $id => $value) : ?>
<option value="<?php echo $id; ?>"><?php echo $value; ?></option>
<?php endforeach; ?>