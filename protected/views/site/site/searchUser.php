<?php $this->renderPartial('search', array('search' => $search)); ?>
<div class="serch_result"><p><?php echo Yii::t('nav', 'SEARCH_RESULTS', array('{search}' => $search)); ?></p></div>
<div class="search users">
	<?php  foreach($users as $id => $user) : ?>
        <div class="spoiler_title search user"><?php $user->render(\app\models\users\User::NAME_NAME); ?><div class="undraw"/></div></div>
			<div class="spoiler_body"><?php $user->getManager()->renderGroups('userSkipEmpty'); ?></div>
	<?php endforeach; ?>
	<div class="clear"></div>
</div>
