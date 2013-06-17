<?php if($model->getUrl()) : ?>
<a class="<?php echo $model->getServise(); ?>" href="<?php echo $model->getUrl(); ?>"><span><?php echo $value . ($model->units ? ' ' . \Yii::t('lists', $model->units) : ''); ?></span></a>
<?php else : ?>
<div class="<?php echo $model->getServise(); ?>"><span><?php echo $value . ($model->units ? ' ' . \Yii::t('lists', $model->units) : ''); ?></span></div>
<?php endif ?>