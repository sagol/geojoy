<div class="form">
  <ul class="nav nav-tabs">
  	<li<?php echo $filter == 'all' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_ALL_THREAD'),
  			array('/messages/threads/index', 'filter' => 'all'),
  			array('class' => '')
  	); ?></li>
  	<li<?php echo $filter == 'mark' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_MARK_MESSAGES'),
  			array('/messages/threads/index', 'filter' => 'mark'),
  			array('class' => '')
  	); ?></li>
  	<li<?php echo $filter == 'notread' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_NOT_READ_MESSAGES'),
  			array('/messages/threads/index', 'filter' => 'notread'),
  			array('class' => '')
  	); ?></li>
  	<li<?php echo $filter == 'notreply' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_NOT_REPLY_MESSAGES'),
  			array('/messages/threads/index', 'filter' => 'notreply'),
  			array('class' => '')
  	); ?></li>
  	<li<?php echo $filter == 'reservation' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_RESERVATION_MESSAGES'),
  			array('/messages/threads/index', 'filter' => 'reservation'),
  			array('class' => '')
  	); ?></li>
  	<li<?php echo $filter == 'contacts' ? ' class="active"' : '' ; ?>><?php echo CHtml::link(
  			\Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_CONTACTS'),
  			array('/messages/contacts/index'),
  			array('class' => '')
  	); ?></li>
  </ul>
</div>