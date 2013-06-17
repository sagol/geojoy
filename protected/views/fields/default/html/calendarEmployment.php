<?php
	$weekDays[0] = 'Sun';
	$weekDays[1] = 'Mon';
	$weekDays[2] = 'Tue';
	$weekDays[3] = 'Wed';
	$weekDays[4] = 'Thu';
	$weekDays[5] = 'Fri';
	$weekDays[6] = 'Sat';
	$weekDays[7] = 'Sun';

	$firstSunday = false;

	$date = new \DateTime();
	$curDay = $date->format('d');

	$year = $date->format('Y');
	$month = $date->format('m');
	$date = new \DateTime("$year-$month-01");

	$weekDay = $date->format('N');
	if($firstSunday && $weekDay == 7) $weekDay = 0;
	$data = $model->getData();

	$locale = \Yii::app()->getLocale();
?>
<div class="calendar">
	<ul class="<?php echo $date->format('F') ?>">
		<li class="month"><?php echo $locale->getMonthName($date->format('n'), 'wide', true); ?></li>
		<?php for($d = 1-$firstSunday; $d < 8-$firstSunday; $d++) : ?>
			<li class="<?php echo $weekDays[$d] ?>_title"><?php echo $locale->getWeekDayName($d == 7 ? 0 : $d, 'abbreviated', true); ?></li>
		<?php endfor ?>
		<?php for($d = 1-$firstSunday; $d < $weekDay; $d++) : ?>
			<li class="<?php echo $weekDays[$d] ?>"></li>
		<?php endfor ?>
		<?php for($d = 1; $d <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $d++) : ?>
				<?php
					$title = array();
					if($curDay == $d) $title[] = ' ' . \Yii::t('fields', 'CURRENT_DATE');
					if(!empty($data[$month][$d])) $title[] = ' ' . \Yii::t('fields', 'BUSY');
					if(!empty($title)) $title = implode(', ', $title);
				 ?>
				<li <?php if(!empty($title)) echo ' title="' . $title . '" alt="' . $title . '" '; else echo ' title="' . \Yii::t('fields', 'FREE') . '" alt="' . \Yii::t('fields', 'FREE') . '" '; ?>class="<?php echo $weekDays[$weekDay] ?><?php if(!empty($data[$month][$d])) echo ' busy' ?><?php if($curDay == $d) echo ' now' ?>"><?php echo $d; ?></li>
			<?php
				$weekDay++;
				if($weekDay > 7 ) $weekDay = 1;
			?>
		<?php endfor ?>
	</ul>
	<?php for($i = 1; $i < 12; $i++) : ?>
	<?php
		$month++;
		if($month > 12) {
			$month = 1;
			$year++;
		}
		$date = new \DateTime("$year-$month-01");
		$weekDay = $date->format('N');
		if($firstSunday && $weekDay == 7) $weekDay = 0;
	?>
	<ul class="<?php echo $date->format('F') ?>">
		<li class="month"><?php echo $locale->getMonthName($date->format('n'), 'wide', true); ?></li>
		<?php for($d = 1-$firstSunday; $d < 8-$firstSunday; $d++) : ?>
			<li class="<?php echo $weekDays[$d] ?>_title"><?php echo $locale->getWeekDayName($d == 7 ? 0 : $d, 'abbreviated', true); ?></li>
		<?php endfor ?>
		<?php for($d = 1-$firstSunday; $d < $weekDay; $d++) : ?>
			<li class="<?php echo $weekDays[$d] ?>"></li>
		<?php endfor ?>
		<?php for($d = 1; $d <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $d++) : ?>
			<li <?php if(!empty($data[$month][$d])) echo ' title="' . \Yii::t('fields', 'BUSY') . '" alt="' . \Yii::t('fields', 'BUSY') . '" '; else echo ' title="' . \Yii::t('fields', 'FREE') . '" alt="' . \Yii::t('fields', 'FREE') . '" '; ?>class="<?php echo $weekDays[$weekDay] ?><?php if(!empty($data[$month][$d])) echo ' busy' ?>"><?php echo $d; ?></li>
			<?php
				$weekDay++;
				if($weekDay > 7 ) $weekDay = 1;
			?>
		<?php endfor ?>
	</ul>
	<?php endfor ?>
</div>