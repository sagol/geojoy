<?php
	$jquery = <<<JQUERY
	var leftButtonDown = false;
	var setBusy = false;
	var startElem = null;
	var startDay = null;
	var startBusy = null;
	var curElem = null;

	$($('#{$model->getName()}')[0].form).submit(function(){
		var calendar = '';
		$('.calendar li.busy[data-month]').each(function(i, elem){
			calendar += elem.getAttribute('data-month') + '-' +  elem.getAttribute('data-day') + ';';
		});
		$('#{$model->getName()}').val(calendar);
	});

	$('*').mouseup(function(e) {
		if(e.which === 1) leftButtonDown = false;
		if(startElem != null && startElem.length) startElem.parent().find('li.inv').removeClass('inv');
	});

	$('.calendar li').each(function(index, elem) {
		elem.onselectstart = function() { return false; }; // IE, Chrome, Safari
		elem.unselectable = "on"; // IE, Opera
	});


	$('.calendar li[data-month]').mousedown(function(e) {
		if(e.which === 1) {
			leftButtonDown = true;
			startElem = curElem = $(e.target);
			startDay = parseInt(startElem.attr('data-day'));
			if(startElem.hasClass('busy')) startElem.parent().find('li:not(.busy)').addClass('inv');
			else startElem.parent().find('li.busy').addClass('inv');
			startElem.toggleClass('busy');
			startBusy = startElem.hasClass('busy');

			setBusy = true;
		}
	})
	.mouseleave(function(e) {
		setBusy = false;
	})
	.mousemove(function(e) {
		if(leftButtonDown && !setBusy) {
			var stopElem = $(this);
			if(curElem.attr('data-month') == stopElem.attr('data-month')) {
				curDay = parseInt(curElem.attr('data-day'));
				stopDay = parseInt(stopElem.attr('data-day'));
				var cur = curElem;

				// движение влево/право на один день
				if(startDay == curDay && (curDay+1 == stopDay || curDay-1 == stopDay)) {
					if(stopElem.hasClass('inv')) stopElem.toggleClass('busy');
					else if(startBusy) stopElem.addClass('busy');
					else stopElem.removeClass('busy');
				}
				// движение вверх от страрта
				else if(startDay >= curDay && curDay > stopDay) {
					for(var i = curDay-1; i >= stopDay; i--) {
						cur = cur.prev();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.addClass('busy');
						else cur.removeClass('busy');
					}
				}
				// движение вниз к страрту (возвращение)
				else if(startDay <= curDay && curDay < stopDay) {
					for(var i = curDay+1; i <= stopDay; i++) {
						cur = cur.next();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.addClass('busy');
						else cur.removeClass('busy');
					}
				}
				// движение вниз от страрта
				else if(startDay >= stopDay && curDay < stopDay) {
					if(curElem.hasClass('inv')) curElem.toggleClass('busy');
					else if(startBusy) curElem.removeClass('busy');
					else curElem.addClass('busy');
					for(var i = curDay+1; i < stopDay; i++) {
						cur = cur.next();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.removeClass('busy');
						else cur.addClass('busy');
					}
				}
				// движение вверх к страрту (возвращение)
				else if(startDay <= stopDay && curDay > stopDay) {
					if(curElem.hasClass('inv')) curElem.toggleClass('busy');
					else if(startBusy) curElem.removeClass('busy');
					else curElem.addClass('busy');
					for(var i = curDay-1; i > stopDay; i--) {
						cur = cur.prev();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.removeClass('busy');
						else cur.addClass('busy');
					}
				}
				// движение вперед, а потом вверх
				else if(startDay < curDay && curDay > stopDay) {
					// убираем следы движения вперед
					if(curElem.hasClass('inv')) curElem.toggleClass('busy');
					else if(startBusy) curElem.removeClass('busy');
					else curElem.addClass('busy');
					for(var i = curDay-1; i > startDay; i--) {
						cur = cur.prev();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.removeClass('busy');
						else cur.addClass('busy');
					}
					// рисуем вверх
					cur = startElem;
					for(var i = startDay-1; i >= stopDay; i--) {
						cur = cur.prev();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.addClass('busy');
						else cur.removeClass('busy');
					}
				}
				// движение назад, а потом ввниз
				else if(startDay > curDay && curDay < stopDay) {
					// убираем следы движения назад
					if(curElem.hasClass('inv')) curElem.toggleClass('busy');
					else if(startBusy) curElem.removeClass('busy');
					else curElem.addClass('busy');
					for(var i = curDay+1; i < startDay; i++) {
						cur = cur.next();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.removeClass('busy');
						else cur.addClass('busy');
					}
					// рисуем ввниз
					cur = startElem;
					for(var i = startDay+1; i <= stopDay; i++) {
						cur = cur.next();
						if(cur.hasClass('inv')) cur.toggleClass('busy');
						else if(startBusy) cur.addClass('busy');
						else cur.removeClass('busy');
					}
				}

				curElem = stopElem;
			}
			else leftButtonDown = false;

			setBusy = true;
		}
	});
JQUERY;

	$cs = \Yii::app()->getClientScript();
	// скрипт инициализации
	$cs->registerScript('calendar-' . $model->getName(), $jquery);

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
<div class="control-group">
	<?php // echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->hiddenField($model, $model->getName(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
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
					<li class="<?php echo $weekDays[$weekDay] ?><?php if(!empty($data[$month][$d])) echo ' busy' ?><?php if($curDay == $d) echo ' now' ?>" data-month="<?php echo $month; ?>" data-day="<?php echo $d; ?>"><?php echo $d; ?></li>
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
				<li class="<?php echo $weekDays[$weekDay] ?><?php if(!empty($data[$month][$d])) echo ' busy' ?>" data-month="<?php echo $month; ?>" data-day="<?php echo $d; ?>"><?php echo $d; ?></li>
				<?php
					$weekDay++;
					if($weekDay > 7 ) $weekDay = 1;
				?>
			<?php endfor ?>
		</ul>
		<?php endfor ?>
	</div>
</div>