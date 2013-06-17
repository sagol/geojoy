<?php $this->breadcrumbs += array($model->value('title')); ?>
<div class="object">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#object" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT')?></a></li>
		<li><a href="#contacts" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_CONTACTS')?></a></li>
	</ul>
	<div class="clear"></div>	<div class="tab-content">
		<div class="tab-pane active" id="object">
			<div class="status">
				<?php echo $status; ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="tab-pane" id="contacts">
			<?php if(empty($socialAccounts)) : ?>
				<?php $user->getManager()->renderGroups('userSkipEmpty'); ?>
			<?php else : ?>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#profile" data-toggle="tab"><?php echo Yii::t('nav', 'USER_PROFILE')?></a></li>
					<?php foreach($socialAccounts as $id => $sa) : ?>
						<li><a href="#<?php echo $sa['service'] ?>" data-toggle="tab"><?php echo Yii::t('nav', $sa['service'])?></a></li>
					<?php endforeach ?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="profile">
						<?php $user->getManager()->renderGroups('userSkipEmpty'); ?>
					</div>
					<?php foreach($socialAccounts as $id => $sa) : ?>
						<div class="tab-pane" id="<?php echo $sa['service'] ?>">
							<?php $this->renderPartial('app.views.site.user.socialInfo', array('socialInfo' => $sa['social_info'])) ?>
						</div>
					<?php endforeach ?>
				</div>
			<?php endif ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>