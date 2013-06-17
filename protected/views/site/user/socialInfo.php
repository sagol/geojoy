<table class="admin table table-striped">
	<tbody>
<?php
	$class = 'odd';
	foreach($socialInfo as $name => $value) {
		if($name == 'PROFILE_IMAGE') $value = '<img src="' . $value . '">';
		elseif($name == 'LINK') $value = '<a href="' . $value . '">' . Yii::t('nav', 'USER_PROFILE') . '</a>';
		echo '<tr class="' . $class . '">';
			echo '<th>' . Yii::t('user', 'SOCIAL_INFO_' . $name) . '</th>';
			echo '<td>' . $value . '</td>';
		echo '</tr>';
		if($class == 'odd') $class = 'even';
		else $class = 'odd';
	}
?>
	</tbody>
</table>