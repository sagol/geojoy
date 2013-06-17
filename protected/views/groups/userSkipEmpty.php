<table class="admin table table-striped">
	<tbody>
<?php
	$class = 'odd';
	if(!empty($fields))
		foreach($fields as $name => $field) {
			$value = $field->getValueText();
			if($value === null || $value === '' || $value === array()) continue;
			echo '<tr class="' . $class . '">';
				echo '<th>' . Yii::t('user', $field->title) . '</th>';
				echo '<td>' . $field->getManager()->render($name, 'default', 'html', true) . '</td>';
			echo '</tr>';
			if($class == 'odd') $class = 'even';
			else $class = 'odd';
		}
?>
	</tbody>
</table>