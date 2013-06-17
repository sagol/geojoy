<?php
	echo CHtml::openTag($this->tagName, $this->htmlOptions);
	if(!empty($links)) foreach($links as $label => $url) {
		if(is_string($label) || is_array($url)) {
			if($label) {
				$labelTranslate = Yii::t('lists', $label);
				if($labelTranslate == $label) $labelTranslate = Yii::t('nav', $label);
			}
			echo '<li>' . CHtml::link($this->encodeLabel ? CHtml::encode(@$labelTranslate) : @$labelTranslate, $url) . '</li>' . $separator;
		}
		else {
			if($url != '') {
				$url = (string)$url;
				$urlTranslate = Yii::t('lists', $url);
				if($urlTranslate == $url) $urlTranslate = Yii::t('nav', $url);
			}
			echo '<li>' . ($this->encodeLabel ? CHtml::encode(@$urlTranslate) : @$urlTranslate) . '</li>';
		}
	}
	echo CHtml::closeTag($this->tagName);