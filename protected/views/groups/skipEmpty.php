<table class="admin table table-striped">
	<tbody>
<?php
	$class = 'odd';
	if(!empty($fields))
		foreach($fields as $name => $field) {
			$value = $field->getValueText();
			if(empty($value)) continue;
			echo '<tr class="' . $class . '">';
				echo '<th>' . Yii::t('fields', $field->title) . '</th>';
				echo '<td>' . $field->getManager()->render($name, 'detailAdvert', 'html', true) . '</td>';
			echo '</tr>';
			if($class == 'odd') $class = 'even';
			else $class = 'odd';
		}
?>
	</tbody>
</table>