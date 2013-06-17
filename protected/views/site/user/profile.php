<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>

<?php if(empty($socialAccounts)) : ?>
	<div class="form">
		<div class="control-group noline">
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<?php $model->getManager()->renderGroups('userSkipEmpty'); ?>
	</div>
<?php else : ?>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#profile" data-toggle="tab"><?php echo Yii::t('nav', 'USER_PROFILE')?></a></li>
		<?php foreach($socialAccounts as $id => $sa) : ?>
			<li><a href="#<?php echo $sa['service'] ?>" data-toggle="tab"><?php echo Yii::t('nav', $sa['service'])?></a></li>
		<?php endforeach ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="profile">
			<div class="form">
				<div class="control-group noline">
					<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
				</div>

				<?php $model->getManager()->renderGroups('userSkipEmpty'); ?>
			</div>
		</div>
		<?php foreach($socialAccounts as $id => $sa) : ?>
			<div class="tab-pane" id="<?php echo $sa['service'] ?>">
				<?php $this->renderPartial('socialInfo', array('socialInfo' => $sa['social_info'])) ?>
			</div>
		<?php endforeach ?>
	</div>
<?php endif ?>