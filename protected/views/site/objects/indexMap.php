<?php
	foreach($objects as $id => $object) {
		$info = '<div class="object-image thumbnail">' . 
			CHtml::link(
				$object->renderExt('fotos', 'main', 'default', 'html', true),
				array('/site/objects/view', 'id' => $id),
				array('class' => '', 'target' => '_blank')
			) . '</div>' . 
			'<div class="up undraw">' . 
			CHtml::link(
				$object->value('title'),
				array('/site/objects/view', 'id' => $id),
				array('class' => 'title', 'target' => '_blank')
			) . 
			'<p class="location">' . $object->value('country') . ', ' . 
  			$object->value('city') . '</p>' . 
			'<p class="price">' . $object->value('price') . ' ' .
			$object->value('valuta') . '</p>' . 
			'<p class="desc">' . str_replace(array("\r\n", "\n", "\n"), '<br>', $object->value('desc')) . '</p><div class="img-up"></div></div>';
		$info = str_replace('"', '\"', $info);
		$markers[] = '[' . $object->value('map') . ', "' . $info . '"]';
	}
	$markers = 'var markers = [' . implode(',', $markers) . ']';

	$lng = \Yii::app()->getLanguage();
	$id = 'map';
	$idCanvas = $id . 'Canvas';
	$center = '0, 0';

	$jquery = <<<JQUERY
	var mapBrand = null;
	$markers
	var map, markersGoogle = [], markersYandex = [];

	function init(brand) {
		if(typeof(brand) == 'undefined') brand = null;
		if(mapBrand && mapBrand == brand) return true;
		if(mapBrand == null && brand == null) alert('Not found map.');

		if(mapBrand && typeof(mapDelete) == 'function') mapDelete();

		window['init' + brand]();
		mapBrand = brand;
	}

	function initGoogle() {
		if(typeof(google) == 'undefined' || typeof(google.maps) == 'undefined') {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "https://maps.googleapis.com/maps/api/js?sensor=false&language=$lng&callback=mapInitGoogle";
			document.body.appendChild(script);
		}
		else mapInitGoogle();
	}

	function initYandex() {
		if(typeof(ymaps) == 'undefined') {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://api-maps.yandex.ru/2.0/?load=package.full&lang=$lng";
			document.body.appendChild(script);
			var k = 0;
			var t = setInterval(function(){
				k++;
				if(typeof(ymaps) != 'undefined') {
					clearInterval(t);
					ymaps.ready(mapInitYandex);
				}
				if(k > 30) {
					clearInterval(t);
					$('#$idCanvas').text('Error load yandex map api.');
				}
			},100);
		}
		else mapInitYandex();
	}
JQUERY;
	$cs = \Yii::app()->getClientScript();
	$cs->registerScript('mapsInit', $jquery, \CClientScript::POS_HEAD);

	$jquery = <<<JQUERY
	function mapInitGoogle() {
		var mapOptions = {
			zoom: 1,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng($center),
		};

		map = new google.maps.Map(document.getElementById('$idCanvas'), mapOptions);

		if(markersGoogle.length) {
			for(var i = 0; i < markersGoogle.length; i++)
				markersGoogle[i].setMap(map);
		}
		else {
			var infoWindow = new google.maps.InfoWindow;

			google.maps.event.addListener(map, 'click', function() {
				infoWindow.close();
			});

			for(var i = 0; i < markers.length; i++) {
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(markers[i][0], markers[i][1]),
					map: map,
					animation: google.maps.Animation.DROP,
					content: markers[i][2],
				})

				google.maps.event.addListener(marker, 'click', function() {
					var marker = this;
					infoWindow.setContent(marker.content);
					infoWindow.open(map, marker);

					var info = $('#mapCanvas').children(':first').children(':first').children(':first')
						.children(':eq(6)').children(':eq(1)');
					info.addClass('infoWindow');
					var closeButton = info.children(':eq(12)').children(':first');
					if(closeButton.children('img').length) closeButton.children('img').remove();
					closeButton.addClass('closeButton').attr('style', '');
				});
				markersGoogle.push(marker);
			}
		}
	}
JQUERY;
	$cs->registerScript('mapGoogle', $jquery, \CClientScript::POS_HEAD);

	$jquery = <<<JQUERY
	function mapInitYandex() {
		$('#mapCanvas').text('');
		var mapOptions = {
			zoom: 1,
			type: 'yandex#map',
			center: [$center],
			behaviors: ['default', 'scrollZoom'],
		};

		map = new ymaps.Map('$idCanvas', mapOptions);
		map.controls.add('zoomControl');


		if(markersYandex.length) {
			for(var i = 0; i < markersYandex.length; i++)
				map.geoObjects.add(markersYandex[i]);
		}
		else {
			for(var i = 0; i < markers.length; i++) {
				var marker = new ymaps.Placemark([markers[i][0], markers[i][1]],{
					balloonContentBody: markers[i][2],
				});
				map.geoObjects.add(marker);

				markersYandex.push(marker);
			}
		}
	}
JQUERY;


	$cs->registerScript('mapYandex', $jquery, \CClientScript::POS_HEAD);

	if($defaultMap == 'g') $defaultMap = "init('Google');";
	elseif($defaultMap == 'g') $defaultMap = "init('Yandex');";

$mapsButtons = <<<JQUERY
	$defaultMap

	$('.maps-button').click(function() {
		init($(this).attr('data'));

		return false;
	});
JQUERY;
	$cs->registerScript('mapsButtons', $mapsButtons);
?>
	<div class="map">
    <a class="maps-button btn" data="Google" href="">Google</a>
  	<a class="maps-button btn" data="Yandex" href="">Yandex</a>
  	<div id="mapCanvas">initialize map...</div>
	</div>