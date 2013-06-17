<div class="topmenu">
<div class="wrapper width_S header">
<div class="header">
	<?php
		// логотип сайта
		echo CHtml::link(
			'',
			array('/site/objects/index'),
			array('class' => 'logo')
		);
	?>
		<?php /* меню языков сайта */
			$language = Yii::app()->getLanguage(); ?>
			<ul class="lang">
				<li class="dropdown"><a data-toggle="dropdown" href="#"><?php echo $language; ?></a>
					<ul class="dropdown-menu">
						<?php
							if(!empty(\Yii::app()->params['lang'])) {
								$request = Yii::app()->request;
								$baseUrl = $request->getBaseUrl();
								$defaultLanguage = Yii::app()->getDefaultLanguage();
								foreach(\Yii::app()->params['lang'] as $lang)
									if($lang != $language) {
										$pathInfo = $request->getPathInfo();
										if($lang == $defaultLanguage) $url = "$baseUrl/$pathInfo";
										else $url = "$baseUrl/$lang/$pathInfo";
										echo '<li><a href="' . $url . '">' . $lang . '</a></li>';
									}
							}
						?>
						<div class="clear"></div>
					</ul>
				</li>
			</ul>
		<?php /* меню языков сайта */ ?>

	
	
	<?php if(Yii::app()->user->getIsGuest()) : ?>
		<?php $this->widget('\app\components\widgets\Menu', array(
			'htmlOptions' => array('class' => 'nav'),
			'translate' => array('lists', 'nav'),
			'items' => array(
				array('label' => '', 'url' => array('/site/user/login'), 'itemOptions' => array('class' => 'right-menu login_icon')),
			),
		)); ?>
	<?php else : ?>
		<?php $this->widget('\app\components\widgets\Menu', array(
			'htmlOptions' => array('class' => 'nav'),
			'translate' => array('lists', 'nav'),
			'items' => array(
				array(
					'label' => '',
					'url' => array('/site/user/logout'),
					'itemOptions' => array(
						'class' => 'right-menu login_icon',
						'title' => Yii::t('nav', 'NAV_LOGOUT'),
					),
				),
				array(
					'label' => '',
					'url' => array('/admin/admin'),
					'visible' => Yii::app()->user->checkAccess('moder'), 
					'itemOptions' => array(
						'class' => 'right-menu admin_icon',
						'title' => Yii::t('nav', 'NAV_ADMIN'),
					),
				),
				array(
					'label' => '',
					'url' => array('/site/user/objects'),
					'itemOptions' => array(
						'class' => 'right-menu user_icon' . (\app\modules\messages\models\Messages::isNew() ? ' new' : ''),
						'title' => Yii::app()->user->name,
					),
				),
			),
		)); ?>
	<?php endif ?>

  	<?php /* кнопка поиска */
		echo CHtml::link(
			'',
			array('/site/site/search'),
			array('class' => 'search')
		);
	?>

  	<?php /* кнопка создания объявления */
		echo CHtml::link(
			Yii::t('nav', 'NAV_OBJECT_ADD'),
			array('/site/objects/selectCategory'),
			array('class' => 'add_obj')
		);
	?>
	
	<?php /* кнопка на карте/в объявлениях */ $this->widget('\app\components\widgets\OnMapButton');?>
	
	<div class="clear"></div>
	</div>
</div>
	</div>