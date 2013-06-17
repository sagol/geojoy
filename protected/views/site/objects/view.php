<?php

$this->breadcrumbs += array($model->value('title'));
$this->pageTitle = $model->value('title') . ' - Geo Joy';
$pageUrl = $this->createAbsoluteUrl('/site/objects/view', array('id' => $model->idobjects));
$lang = Yii::app()->getLanguage();
$langFacebook = @Yii::app()->params['facebookLangs'][$lang];

if($model->getManager()->hasField('fotos'))
	$this->setHeaderMeta(array('<link rel="image_src" href="' . $model->field('fotos')->mainPhoto() . '" />',));

$cs = Yii::app()->getClientScript();
?>
<div class="object">
	<div>
		<div class='pluso pluso-theme-light'><div class='pluso-more-container'><a class='pluso-more' href=''></a><ul class='pluso-counter-container'><li><li class='pluso-counter'><li></ul></div><a class='pluso-evernote'></a><a class='pluso-email'></a><a class='pluso-facebook'></a><a class='pluso-twitter'></a><a class='pluso-google'></a><a class='pluso-tumblr'></a><a class='pluso-blogger'></a><a class='pluso-pinme'></a><a class='pluso-myspace'></a><a class='pluso-vkontakte'></a><a class='pluso-odnoklassniki'></a><a class='pluso-livejournal'></a><a class='pluso-moimir'></a></div>
		<script type='text/javascript'>if(!window.pluso){pluso={version:'0.9.1',url:'http://share.pluso.ru/'};h=document.getElementsByTagName('head')[0];l=document.createElement('link');l.href=pluso.url+'pluso.css';l.type='text/css';l.rel='stylesheet';s=document.createElement('script');s.src=pluso.url+'pluso.js';s.charset='UTF-8';h.appendChild(l);h.appendChild(s)}</script>
		<?php if(Yii::app()->user->checkAccess('markSpamObjectAndAddBookmarks', array('curUser' => Yii::app()->user, 'multiUser' => $model->multiUser, 'idusers' => $model->idusers))) :
			echo CHtml::ajaxLink(
				'',
				array('/site/objects/spam', 'id' => $model->idobjects),
				array('success' => 'function(html){
					if(html == "ok") alert(html);
				}'),
				array('class' => 'spam', 'confirm' => \Yii::t('nav', 'MARK_TO_SPAM'), 'rel' => 'tooltip', 'title' => Yii::t('nav', 'NAV_OBJECT_DETAIL_MARK_SPAM'))
			);
		endif; ?>
		<?php // вывод кнопки поднятия объявления
			$dateShow = new DateTime($model->show);
			$dateNow = new DateTime('now');
			$interval = $dateShow->diff($dateNow);

			$seconds = ($interval->y * 365 * 24 * 60 * 60) +
			($interval->m * 30 * 24 * 60 * 60) +
			($interval->d * 24 * 60 * 60) +
			($interval->h * 60 *60) +
			$interval->s;

			if($model->multiUser == Yii::app()->user->multiUser && $seconds >= Yii::app()->params['objectUpTime']) echo CHtml::ajaxLink(
				Yii::t('nav', 'NAV_OBJECT_UP'),
				array('/site/objects/objectUp', 'id' => $model->idobjects),
				array('success' => 'function(html){alert(html);}'),
				array('class' => 'btn moveup')
			);
		?>
		<div class="clear"></div>
	</div>
	<?php if($status) : ?>
		<div class="status">
			<?php echo $status; ?>
		</div>
	<?php endif ?>
	<div class="clear"></div>
	<?php
		$fotos = $model->render('fotos', 'detailAdvert', 'html', true);
		if($model->getManager()->hasField('map')) $map = $model->field('map')->getOnMap();
		else $map = false;
		$calendar = $model->render('calendar', 'detailAdvert', 'html', true);
	?>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#object" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT')?></a></li>
		<?php if($fotos) : ?>
			<li><a href="#photo" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_PHOTO')?></a></li>
		<?php endif ?>
		<?php if($map) : ?>
			<li><a href="#map" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_ON_MAP')?></a></li>
		<?php endif ?>
		<li><a href="#parameters" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_PARAMETERS_OF_APARTMENT')?></a></li>
		<?php if($calendar) : ?>
			<li><a href="#calendar" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_CALENDAR')?></a></li>
		<?php endif ?>
		<li><a href="#contacts" data-toggle="tab"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_CONTACTS')?></a></li>
		<?php if(!Yii::app()->user->getIsGuest()) : ?>
			<li class="dropdown" id="actions">
				<a data-toggle="dropdown" href="#actions"><?php echo Yii::t('nav', 'NAV_OBJECT_DETAIL_ACTION')?></a>
				<ul class="dropdown-menu">
					<?php if($checkAccessEditOrDeleteObject) : ?>
						<li><?php echo CHtml::link(
							Yii::t('nav', 'NAV_OBJECT_EDIT'),
							array('/site/objects/edit', 'id' => $model->idobjects)
							); ?></li>
						<li><?php echo CHtml::link(
							Yii::t('nav', 'NAV_OBJECT_DEL'),
							array('/site/objects/del', 'id' => $model->idobjects),
							array('confirm' => \Yii::t('nav', 'DELETE_ELEMENT'))
						);?></li>
						<?php $divider = true; ?>
					<?php endif ?>
					<?php if(Yii::app()->user->checkAccess('markSpamObjectAndAddBookmarks', array('curUser' => Yii::app()->user, 'multiUser' => $model->multiUser, 'idusers' => $model->idusers))) : ?>
						<?php if(@$divider) : ?><li class="divider"></li><?php endif ?>
						<li><?php 
							$cs = \Yii::app()->getClientScript();
							$url = $this->createUrl('/site/bookmarks/add', array('id' => $model->idusers));
							$handler = 'jQuery("#actions").removeClass("open");jQuery.ajax({"url":"' .
							$url .
							'","cache":false, "success": function(html){alert(html);}});return false;';

							$cs->registerScript('Yii.CHtml.#bookmarks', "$('body').on('click', '#bookmarks', function(){{$handler}});");

							echo CHtml::link(
							Yii::t('nav', 'NAV_OBJECT_DETAIL_BOOKMARK'),
							'#',
							array('id' => 'bookmarks')
						);?></li>
						<li><?php 
							$handler = 'jQuery("#actions").removeClass("open");if(confirm("' . \Yii::t('nav', 'MARK_TO_SPAM') . '")){jQuery.ajax({"url":"' .
							$this->createUrl('/site/objects/spam', array('id' => $model->idobjects)) .
							'","cache":false});};return false;';

							$cs->registerScript('Yii.CHtml.#spam', "$('body').on('click', '#spam', function(){{$handler}});");

							echo CHtml::link(
							Yii::t('nav', 'NAV_OBJECT_DETAIL_MARK_SPAM'),
							'#',
							array('id' => 'spam')
						);?></li>
					<?php endif ?>
						<li><?php 
							echo CHtml::link(
							Yii::t('nav', 'NAV_OBJECT_DETAIL_PRINT'),
							array('/site/objects/view/', 'id' => $model->idobjects, 'print' => 'on'),
							array('class' => '', 'target' => '_blank')
						);?></li>
				</ul>
			</li>
		<?php endif ?>
	</ul>
	<div class="clear"></div>
	<div class="tab-content">
		<div class="tab-pane active" id="object">
		<div class="object-image thumbnail">
			<?php /*Картинка объявления*/ echo $model->renderExt('fotos', 'main'); ?>
		</div>
		<div class="object-text">
			<p class="price"><?php /*Цена*/ $model->render('price'); ?>
			<span class="valuta"><?php /*Валюта*/ $model->render('valuta'); ?></span></p>
			<p  class="location"><?php /*Страна*/ $model->render('country'); ?>,
			<?php /*Город*/ $model->render('city'); ?></p>
			<p class="title"><?php $model->render('title'); ?></p>
			<p><?php $model->render('full_description'); ?></p>
		</div>
  			
			<?php if(!Yii::app()->user->getIsGuest() && $model->idusers != Yii::app()->user->id) : ?>
				<?php $this->widget('\app\modules\messages\components\widgets\Message', array(
					'action' => array('/messages/messages/writer', 'id' => $model->idobjects),
				)); ?>
			<?php endif; ?>
  		
			<div class="clear"></div>
		</div>
		<?php if($fotos) : ?>
			<div class="tab-pane" id="photo">
				<?php echo $fotos; ?>
			</div>
		<?php endif ?>
		<div class="tab-pane" id="parameters">
			<?php $model->getManager()->renderGroup('NLS_LOCATION', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_SPECIFICATION', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_ADDITIONAL_INFO', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_INDOOR_FACILITIES', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_OUTDOOR_FACILITIES', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_ABOUT_OWNER', 'skipEmpty'); ?>
			<?php $model->getManager()->renderGroup('NLS_NOTICES', 'skipEmpty'); ?>
		</div>
		<?php if($map) : ?>
			<div class="tab-pane" id="map">
				<?php
					if($this->cacheBlockBegin('objectsOnMap-' . $model->idobjects, array('duration' => \Yii::app()->params['cache']['objectsOnMap']))) {
						$this->widget('\app\components\widgets\ObjectsOnMap', array(
							'object' => $model,
							'criteriaFields' => array('country', 'city'),
						));
						$this->cacheBlockEnd();
					}
				?>
			</div>
		<?php endif ?>
		<?php if($calendar) : ?>
			<div class="tab-pane" id="calendar">
				<?php echo $calendar; ?>
			</div>
		<?php endif ?>
		<div class="tab-pane" id="contacts">
			<?php if($this->cacheBlockBegin('user-info-' . $lang . '-' . $user->idusers, array(
					'duration' => \Yii::app()->params['cache']['users'],
					'varyByValue' => $user->getManager()->checkAccessUser(),
				))) : ?>
				<?php if(empty($socialAccounts)) : ?>
					<?php $user->getManager()->renderGroups('userSkipEmpty'); ?>
				<?php else : ?>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#profile" data-toggle="tab"><?php echo Yii::t('nav', 'USER_PROFILE')?></a></li>
						<?php foreach($socialAccounts as $id => $sa) : ?>
							<li><a href="#<?php echo $sa['service'] ?>" data-toggle="tab"><?php echo Yii::t('nav', $sa['service'])?></a></li>
						<?php endforeach ?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="profile">
							<?php $user->getManager()->renderGroups('userSkipEmpty'); ?>
						</div>
						<?php foreach($socialAccounts as $id => $sa) : ?>
							<div class="tab-pane" id="<?php echo $sa['service'] ?>">
								<?php $this->renderPartial('app.views.site.user.socialInfo', array('socialInfo' => $sa['social_info'])) ?>
							</div>
						<?php endforeach ?>
					</div>
				<?php endif ?>
				<?php $this->cacheBlockEnd(); ?>
			<?php endif ?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php
	if($this->cacheBlockBegin('similarOffers-' . $lang . '-' . $model->idobjects, array('duration' => \Yii::app()->params['cache']['similarOffers']))) {
		$this->widget('\app\components\widgets\SimilarOffers', array(
			'object' => $model,
			'criteriaFields' => array('country', 'city'),
		));
		$this->cacheBlockEnd();
	}
?>