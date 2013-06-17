<?php $this->renderPartial('search', array('search' => $search)); ?>
<div class="serch_result"><p><?php echo Yii::t('nav', 'SEARCH_RESULTS', array('{search}' => $search)); ?></p></div>
<h4><?php echo Yii::t('nav', 'SEARCH_RESULTS_NO'); ?></h4>