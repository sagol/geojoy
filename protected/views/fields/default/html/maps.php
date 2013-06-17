<?php
if(!empty($value)) :
	$lng = \Yii::app()->getLanguage();
	$mapsInit = <<<JQUERY
	var mapBrand = null;

	function init(brand) {
		if(typeof(brand) == 'undefined') brand = null;
		if(mapBrand && mapBrand == brand) return true;
		if(mapBrand == null && brand == null) alert('Not found map.');

		if(mapBrand && typeof(mapDelete) == 'function') mapDelete();
		window['init' + brand]();
		mapBrand = brand;
	}
JQUERY;

	$cs = \Yii::app()->getClientScript();
	$id = $model->getName();
	$idCanvas = $id . 'Canvas';
	$coord = $model->getCoord();

	if($model->getBrand() == 'Google') {
		$cs->registerScriptFile('https://maps.googleapis.com/maps/api/js?sensor=false&language=' . $lng, \CClientScript::POS_HEAD);
		$jquery = <<<JQUERY
	var map;
	function mapInitGoogle() {
		var marker;
		var mapOptions = {
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng($coord),
		};

		map = new google.maps.Map(document.getElementById('$idCanvas'), mapOptions);

		marker = new google.maps.Marker({
			position: new google.maps.LatLng($coord),
			map: map,
			animation: google.maps.Animation.DROP,
		});
	}
	mapInitGoogle();
JQUERY;
	}
	elseif($model->getBrand() == 'Yandex') {
		$cs->registerScriptFile('http://api-maps.yandex.ru/2.0/?load=package.full&lang=' . $lng, \CClientScript::POS_HEAD);
		$jquery = <<<JQUERY

	var map;
	function mapInitYandex() {
		if(typeof(ymaps) == 'undefined') {
			var k = 0;
			var t = setInterval(function(){
				k++;
				if(typeof(ymaps) != 'undefined') {
					clearInterval(t);
					ymaps.ready(mapYandex);
				}
				if(k > 30) {
					clearInterval(t);
					$('#$idCanvas').text('Error load yandex map api.');
					return false;
				}
			},100);
		}
		else ymaps.ready(mapYandex);
	}

	function mapYandex() {
		var marker;
		$('#mapCanvas').text('');
		var mapOptions = {
			zoom: 10,
			type: 'yandex#map',
			center: [$coord],
			behaviors: ['default', 'scrollZoom'],
		};

		map = new ymaps.Map('$idCanvas', mapOptions);
		map.controls.add('zoomControl');

		marker = new ymaps.Placemark([$coord]),
		map.geoObjects.add(marker);
	}
	mapInitYandex();
JQUERY;
	}

	$cs->registerScript('maps', $jquery, \CClientScript::POS_READY);
?>
	<div id="<?php echo $model->getName(); ?>Canvas" style="">initialize map...</div>
<?php endif; ?>