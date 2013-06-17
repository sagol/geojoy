
<form class="form-search" action="<?php echo $this->createUrl('/site/site/search'); ?>">
	<p class="note"><?php echo Yii::t('nav', 'SEARCH_DESCRIPTIOM'); ?></p>
  <input type="text" class="input-medium search-query" name="search" value="<?php echo $search; ?>">
	<button type="submit" class="btn btn-primary"><?php echo Yii::t('nav', 'SEARCH'); ?></button>
</form>
