<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>

<ul class="bookmarks"><!--Объявления-->
	<?php foreach($bookmarks as $bookmark) : ?>
		<li>
			<?php echo CHtml::link(
				$bookmark['name'],
				array('/site/bookmarks/show', 'id' => $bookmark['idobj_bookmarks'])
			);?>
			<?php echo CHtml::ajaxLink(
				'',
				array('/site/bookmarks/delete', 'id' => $bookmark['idobj_bookmarks']),
				array('success' => 'function(html){if(html == "ok") window.location.reload();}'),
				array('class' => 'delete', 'confirm' => \Yii::t('nav', 'DELETE_ELEMENT'), 'rel' => 'tooltip', 'title' => Yii::t('nav', 'BOOKMARK_DELETE'))
			);?>

		 </li>
	<?php endforeach; ?>
</ul>