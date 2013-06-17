<?php $this->beginContent('//layouts/site'); ?>
	<div class="wrapper width_S content"><!--Оболочка-->

		<!--Контент-->
			<?php // хлебные крошки
				$this->widget('\app\components\widgets\Breadcrumbs', array(
					'links' => $this->breadcrumbs,
				));
			?>
			
			<?php echo $content; ?>

			<?php // страницы
				$this->widget('\app\components\widgets\Pages', array(
					'pages' => $this->pages,
				));
			?>
		
		<div class="clear"></div>
	</div>
<?php $this->endContent(); ?>