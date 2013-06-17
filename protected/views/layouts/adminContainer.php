<?php $this->beginContent('//layouts/admin'); ?>
	<div class="container admincontent"><!--Оболочка для левой колонки и контента-->
		<div class="leftbar"><!--Левая колонка-->
			<?php
				// название блока меню
				$this->beginWidget('zii.widgets.CPortlet', array(
					'title' => \Yii::t('admin', 'MENU_CONTROL'),
				));
				// главное левое меню
				$this->widget('zii.widgets.CMenu', array(
					'items' => $this->mainMenu,
					'htmlOptions' => array('class' => 'operations'),
				));
				$this->endWidget();

				// название блока меню
				$this->beginWidget('zii.widgets.CPortlet', array(
					'title' => $this->menuTitle,
				));
				// меню операций
				$this->widget('zii.widgets.CMenu', array(
					'items' => $this->menu,
					'htmlOptions' => array('class' => 'operations'),
				));
				$this->endWidget();
			?>
		</div>
		<div class="rightbar"><!--Контент-->
			<?php // хлебные крошки
				$this->widget('\app\components\widgets\Breadcrumbs', array(
					'links' => $this->breadcrumbs,
					'homeLink' => false,
				));
			?>
			
			<?php echo $content; ?>

			<?php // страницы
				$this->widget('\app\components\widgets\Pages', array(
					'pages' => $this->pages,
				));
			?>
		</div>
		<div class="clear"></div>
	</div>
<?php $this->endContent(); ?>