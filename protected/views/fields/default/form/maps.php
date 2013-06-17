<?php
	$coord = $model->getCoord();
	$lng = \Yii::app()->getLanguage();
	$id = $model->getName();
	$idCanvas = $id . 'Canvas';
	$jquery = <<<JQUERY
	var map, geocoder, marker, markerCoord, mapZoom;

	function mapInitGoogle() {
		var mapOptions = {
			zoom: 1,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng(0, 0)
		};

		map = new google.maps.Map(document.getElementById('$idCanvas'), mapOptions);
		if(markerCoord) {
			var coords = markerCoord.split(',');
			var latLng = new google.maps.LatLng(coords[0], coords[1]);
			marker = new google.maps.Marker({
				position: latLng,
				map: map,
				animation: google.maps.Animation.DROP,
			});

			map.setCenter(latLng);
			if(mapZoom) map.setZoom(mapZoom);
			$('#$id').val('g:' + markerCoord);
		}
		geocoder = new google.maps.Geocoder();

		google.maps.event.addListener(map, 'click', function(e) {
			if(marker) marker.setPosition(e.latLng);
			else {
				marker = new google.maps.Marker({
					position: e.latLng,
					map: map,
					animation: google.maps.Animation.DROP,
				});
			}

			$('#$id').val('g:' + marker.position.lat() + "," + marker.position.lng());

			geocoder.geocode({latLng: e.latLng}, function reverseGeocodeResult(results, status) {
				if(status == google.maps.GeocoderStatus.OK) {
					if(results.length == 0) $('#{$id}Address').text('None');
					else {
						$('#{$id}Address').text(results[0].formatted_address);
						map.fitBounds(results[0].geometry.viewport);
						// map.setCenter(marker.position);
						// map.setZoom(10);
					}
				}
				else $('#{$id}Address').text('Error');
			});
		});

		function mapMarkerDeleteGoogle() {
			if(marker) {
				marker.setMap(null);
				$('#$id').val('');
				$('#{$id}Address').text('');
				marker = null;
				markerCoord = null;
			}
		}

		function mapButtonFindGoogle(address) {
			geocoder.geocode({
				'address': address,
				'partialmatch': true
				}, 
				function geocodeResult(results, status) {
					var statusCode = [];
					statusCode['OK'] = 'The response contains a valid GeocoderResponse.';
					statusCode['ERROR'] = 'There was a problem contacting the Google servers.';
					statusCode['INVALID_REQUEST'] = 'This GeocoderRequest was invalid.';
					statusCode['OVER_QUERY_LIMIT'] = 'The webpage has gone over the requests limit in too short a period of time.';
					statusCode['REQUEST_DENIED'] = 'The webpage is not allowed to use the geocoder.';
					statusCode['UNKNOWN_ERROR'] = 'A geocoding request could not be processed due to a server error. The request may succeed if you try again.';
					statusCode['ZERO_RESULTS'] = 'No result was found.';
					if (status == google.maps.GeocoderStatus.OK && results.length > 0) {
						map.fitBounds(results[0].geometry.viewport);
						// map.setCenter(results[0].geometry.location);
					}
					else {
						$('#{$id}Address').text('Geocode was not successful for the following reason: ' + statusCode[status]);
					}
				}
			);
		}

		function mapDeleteGoogle() {
			if(marker) {
				var coord = marker.position.lat() + "," + marker.position.lng();
				mapMarkerDelete();
				mapZoom = map.getZoom();
				markerCoord = coord;
			}
			google.maps.event.clearInstanceListeners(map);
			geocoder = null;
			map = null;

			$('#$idCanvas').text('initialize map...');
			delete mapInit;
			delete mapDelete;
			delete mapButtonFind;
			delete mapMarkerDelete;
		}

		mapInit = mapInitGoogle;
		mapDelete = mapDeleteGoogle;
		mapButtonFind = mapButtonFindGoogle;
		mapMarkerDelete = mapMarkerDeleteGoogle;
	}


	function mapInitYandex() {
		$('#$idCanvas').text('');
		var mapOptions = {
			zoom: 1,
			type: 'yandex#map',
			center: [0, 0],
			behaviors: ['default', 'scrollZoom'],
		};

		map = new ymaps.Map('$idCanvas', mapOptions);

		if(markerCoord) {
			var coords = markerCoord.split(',');
			marker = new ymaps.Placemark(coords),
			map.geoObjects.add(marker);
			if(mapZoom) map.setCenter(coords, mapZoom);
			else map.setCenter(coords);
			$('#$id').val('y:' + markerCoord);
		}

		map.events.add('click', function (e) {
			var coords = e.get('coordPosition');
			if(marker) marker.geometry.setCoordinates(coords);
			else {
				marker = new ymaps.Placemark(coords),
				map.geoObjects.add(marker);
			}

			map.panTo(coords);

			ymaps.geocode(coords).then(function (res) {
				var names = [];
				res.geoObjects.each(function (obj) {
					names.push(obj.properties.get('name'));
				});
				map.setCenter(coords);
				$('#$id').val('y:' + coords);
				$('#{$id}Address').text(names.reverse().join(', '));
			});

		});

		map.controls.add('zoomControl');

		function mapMarkerDeleteYandex() {
			if(marker && map) {
				map.geoObjects.remove(marker);
				marker = null;
				$('#$id').val('');
				$('#{$id}Address').text('');
				markerCoord = null;
			}
		}

		function mapButtonFindYandex(address) {
			ymaps.geocode(address, { results: 1, json: true }).then(function (res) {
				var bounds = res.GeoObjectCollection.featureMember[0].GeoObject.boundedBy.Envelope,
					b1 = bounds.lowerCorner.split(' ').reverse(),
					b2 = bounds.upperCorner.split(' ').reverse();
				map.setBounds([b1, b2], {checkZoomRange: true, precizeZoom: true});
				// json: false
				// map.setBounds(res.geoObjects.get(0).geometry.getBounds(), {checkZoomRange: true, precizeZoom: true});
			}, function (err) {
				$('#{$id}Address').text(err.message);
			});
		}

		function mapDeleteYandex() {
			if(marker) {
				var coord = marker.geometry.getCoordinates();
				coord = coord[0] + ',' + coord[1];
				mapMarkerDelete();
				mapZoom = map.getZoom();
				markerCoord = coord;
			}
			map.destroy();
			map = null;

			$('#$idCanvas').text('initialize map...');
			delete mapInit;
			delete mapDelete;
			delete mapButtonFind;
			delete mapMarkerDelete;
		}

		mapInit = mapInitYandex;
		mapDelete = mapDeleteYandex;
		mapButtonFind = mapButtonFindYandex;
		mapMarkerDelete = mapMarkerDeleteYandex;
	}
JQUERY;
	$brand = $model->getBrand();
	$mapsButtons = <<<JQUERY
	init('$brand');

	$('#{$id}FindAddress').keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13') {
			event.preventDefault();
			if(typeof(mapButtonFind) == 'function') mapButtonFind(document.getElementById("{$id}FindAddress").value);
		}
	});

	$('#mapButtonFind').click(function(){
		if(typeof(mapButtonFind) == 'function') mapButtonFind(document.getElementById("{$id}FindAddress").value);
	});

	$('#mapButtonDel').click(function(){
		if(typeof(mapMarkerDelete) == 'function') mapMarkerDelete();
	});

	$('.maps-button').click(function(){
		init($(this).attr('data'));

		return false;
	});
JQUERY;

	$mapsInit = <<<JQUERY
	var mapBrand = null;

	function init(brand) {
		markerCoord = '$coord';
		if(markerCoord && typeof(mapZoom) == 'undefined') mapZoom = 10;

		if(typeof(brand) == 'undefined') brand = null;
		if(mapBrand && mapBrand == brand) return true;
		if(mapBrand == null && brand == null) alert('Not found map.');

		if(mapBrand && typeof(mapDelete) == 'function') mapDelete();
		window['init' + brand]();
		mapBrand = brand;
	}

	function initGoogle() {
		if(typeof(google) == 'undefined'|| typeof(google.maps) == 'undefined') {
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
	// скрипт инициализации
	$cs->registerScript('maps', $jquery, \CClientScript::POS_HEAD);
	$cs->registerScript('mapsButtons', $mapsButtons);
	$cs->registerScript('mapsInit', $mapsInit, \CClientScript::POS_HEAD);
?>

<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->hiddenField($model, $model->getName(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>
<div class="control-group">
  <label class="control-label"><?php echo \Yii::t('fields', 'FIND_ON_MAP'); ?></label>
  <div class="map_search">
    <input type="text" id="<?php echo $model->getName(); ?>FindAddress"/>
    <input id="mapButtonFind" type="button" value="<?php echo \Yii::t('fields', 'BUTTON_FIND'); ?>" class="btn">
  </div>
</div>
<div class="map">
	<div id="<?php echo $model->getName(); ?>Address"></div>
	<div id="<?php echo $model->getName(); ?>Canvas">initialize map...</div>
	<a class="maps-button btn" data="Google" href="">Google</a>
	<a class="maps-button btn" data="Yandex" href="">Yandex</a>
  <input id="mapButtonDel" type="button" value="<?php echo \Yii::t('fields', 'DELETE_MARKER'); ?>" class="btn">
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>