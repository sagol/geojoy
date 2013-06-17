	<div id="left-button-tiles"></div>
	<div id="tiles">
		<?php
			echo CHtml::openTag('ul', $this->htmlOptions);
			foreach($categories as $id => $category) {
				$label = Yii::t('lists', $category['label']);
				$text = $this->encodeLabel ? CHtml::encode($label) : $label;
				$text = '<img src="' . $category['img'] . '"><p>' . $text . '</p>';

				echo '<li class="' . $category['alias'] . ($active == $id ? ' active' : '') . '">' . 
				CHtml::link($text, $category['url']) .
				'</li>';
			}
			echo CHtml::closeTag('ul');
		?>
	</div>
	<div id="right-button-tiles"></div>