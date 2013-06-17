<?php
	echo CHtml::openTag('ul', $this->htmlOptions);
	if($main) foreach($fields as $idFields => $values) {

		foreach($values['values'] as $id => $value) {

			$label = Yii::t('lists', $value['value']['value']);
			echo '<li>' . CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $value['value']['url']);
			if(!empty($value['values'])) $this->render('filter', array(
				'values' => $value,
				'main' => false,
			));
			echo '</li>';
		}
	}
	else {
		foreach($values['values'] as $id => $value) {

			$label = Yii::t('lists', $value['value']['value']);
			echo '<li>' . CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $value['value']['url']);
			if(!empty($value['values'])) $this->render('filter', array(
				'values' => $value,
				'main' => false,
			));
			echo '</li>';
		}
	}
	echo CHtml::closeTag('ul');