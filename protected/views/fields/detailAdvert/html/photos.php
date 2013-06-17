<?php
$imgs = $model->data('url');
if(!empty($imgs)) :
	$baseUrl = Yii::app()->request->getBaseUrl();
	\Yii::app()->getClientScript()->registerScriptFile($baseUrl . '/js/upload/JavaScript-Load-Image/load-image.min.js', \CClientScript::POS_END);
	\Yii::app()->getClientScript()->registerScriptFile($baseUrl . '/js/bootstrap-image-gallery.min.js', \CClientScript::POS_END);
	\Yii::app()->getClientScript()->registerCssFile($baseUrl . '/css/bootstrap-image-gallery.min.css');

?>
	<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery-<?php echo $model->name; ?>">
	<?php
		$i = 1;
		foreach($imgs as $url) {
			$f = strrpos($url, DS);
			$src = substr($url, 0, $f+1) . 'thumbnails' . substr($url, $f);
			echo '<a class="thumbnail" href="' . $url . '" title="Foto ' . $i . '" rel="gallery"><img src="' . $src . '" /></a>';
			$i++;
		}
	?>
		<div class="clear"></div>
	</div>
	<div id="modal-gallery-<?php echo $model->name; ?>" class="modal modal-gallery hide fade">
		
		<table>
		<tbody>
		<tr>
    <td>
    <div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3 class="modal-title"></h3>
		</div>
		
      <div class="modal-image">
        
      </div>
    
		<div class="modal-footer">
			<a class="btn btn-primary modal-next"><?php echo \Yii::t('nav', 'BUTTON_MODAL_NEXT'); ?> <i class="icon-arrow-right icon-white"></i></a>
			<a class="btn btn-info modal-prev"><i class="icon-arrow-left icon-white"></i> <?php echo \Yii::t('nav', 'BUTTON_MODAL_PREV'); ?></a>
			<a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000"><i class="icon-play icon-white"></i> <?php echo \Yii::t('nav', 'BUTTON_MODAL_SLIDESHOW'); ?></a>
		</div>
		</td>
    </tr>
		</tbody>
		</table>
		
	</div>
<?php

endif;

?>