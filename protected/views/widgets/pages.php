<?php
	echo CHtml::openTag($this->tagName, $this->htmlOptions);
	echo  '<ul>';

	if($previous) {
		$previous['title'] = Yii::t('nav', $previous['title']);
		if(@$previous['url']) echo '<li class="prev">' . CHtml::link($previous['title'], $previous['url']) . '</li>';
		else  echo '<li class="prev disabled">' . CHtml::link($previous['title']) . '</li>';
	}

	if(!empty($pages)) foreach($pages as $page) {
		if(@$page['separator']) echo '<li>' . CHtml::link($separator, '#') . '</li>';
		elseif(@$page['url']) echo '<li>' . CHtml::link($this->encodeLabel ? CHtml::encode($page['title']) : $page['title'], $page['url']) . '</li>';
		else echo '<li class="active">' .  CHtml::link($this->encodeLabel ? CHtml::encode($page['title']) : $page['title'], '#') . '</li>';
	}

	if($next) {
		$next['title'] = Yii::t('nav', $next['title']);
		if(@$next['url']) echo '<li class="next">' . CHtml::link($next['title'], $next['url']) . '</li>';
		else echo '<li class="next disabled">' . CHtml::link($next['title']) . '</li>';
	}

	echo '</ul>';
	echo CHtml::closeTag($this->tagName);