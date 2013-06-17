function size() {
	var wrapperClass, wrapperWidth;
	var small = 700, medium = 996, large = 1296;

	if($('.width_S').length) {
		wrapperWidth = small;
		wrapperClass = 'width_S';
	}
	else if($('.width_M').length) {
		wrapperWidth = medium;
		wrapperClass = 'width_M';
	}
	else {
		wrapperWidth = large;
		wrapperClass = 'width_L';
	}

	var size = $(window).width();

	var wrapperClassNew;
	if(size < medium) wrapperClassNew = 'width_S';
	else if(size < large) wrapperClassNew = 'width_M';
	else wrapperClassNew = 'width_L';
	if(wrapperClass != wrapperClassNew)
		$('.' + wrapperClass).removeClass(wrapperClass).addClass(wrapperClassNew);
}

var maxShowChild, childCount;
function tiles() {
	var wrapper = $('.wrapper.categor');
	if(wrapper.hasClass('width_S')) maxShowChild = 3;
	else if(wrapper.hasClass('width_M')) maxShowChild = 4;
	else maxShowChild = 5;

	var children = $('#tiles li');
	var childWidth = children.width();
	childCount = children.length;

	ul = $('#tiles ul');
	ul.css('width', childWidth*childCount);

	var tile = $.cookie('tile');
	if(tile == null) tile = 1;
	else tile = parseInt(tile);
	if(tile > childCount) tile = 1;

	$.cookie('tile', tile, { expires: 360 });

	if(tile > 1) {
		children.each(function(i, elem){
			if(i < tile-1 || i >= tile-1 + maxShowChild) $(elem).addClass('hide');
		});
	}

	if(tile > 1) $('#left-button-tiles').removeClass('hide');
	else $('#left-button-tiles').addClass('hide');

	if(tile - 1 + maxShowChild == childCount) $('#right-button-tiles').addClass('hide');
	else $('#right-button-tiles').removeClass('hide');

	if(childCount <= maxShowChild) {
		$('#left-button-tiles').addClass('hide');
		$('#right-button-tiles').addClass('hide');
		$('#tiles li.hide').removeClass('hide');
	}
}


$(window).resize(function(){
	size();
	tiles();
});


$(window).scroll(function () {
	if($(this).scrollTop() > 100) $('#win_up').fadeIn();
	else $('#win_up').fadeOut();
});


jQuery(function($) {
	size();
	tiles();


	$('#left-button-tiles').click(function(){
		var tile = $.cookie('tile');
		if(tile == null) tile = 1;
		else tile = parseInt(tile);
		tile = tile - 1;
		if(tile < 1) tile = 1;
		$.cookie('tile', tile, { expires: 360 });

		if(tile == 1 )  $('#left-button-tiles').addClass('hide');
		else $('#left-button-tiles').removeClass('hide');

		if(tile - 1 + maxShowChild < childCount) $('#right-button-tiles').removeClass('hide');

		$('#tiles .category li:nth-child(' + tile + ')').removeClass('hide');
		tile = tile + maxShowChild;
		$('#tiles .category li:nth-child(' + tile + ')').addClass('hide');
	});


	$('#right-button-tiles').click(function(){
		var tile = $.cookie('tile');
		if(tile == null) tile = 1;
		else tile = parseInt(tile);
		tile = tile + 1;
		$.cookie('tile', tile, { expires: 360 });

		if(tile - 1 + maxShowChild >= childCount) $('#right-button-tiles').addClass('hide');
		else $('#right-button-tiles').removeClass('hide');

		if(tile > 1 )  $('#left-button-tiles').removeClass('hide');

		tile = tile - 1;
		$('#tiles .category li:nth-child(' + tile + ')').addClass('hide');
		tile = tile + maxShowChild;
		$('#tiles .category li:nth-child(' + tile + ')').removeClass('hide');
	});


	$('body').on('click', '.undraw', function() {
		$(this).parent().next().toggleClass('open');
		$(this).toggleClass('open');
	});


	$('#win_up').click(function () {
		$('html,body').animate({scrollTop:0}, 'slow');
	});


	$('#hide_footer').click(function () {
		var marginBottom = $('.content').css('margin-bottom');
		$('.footer').toggleClass('visible');
		$('#hide_footer').toggleClass('footer_down');
		$('#hide_footer').toggleClass('footer_up');
		var height = $('.footer').css('height');
		if(!parseInt(marginBottom)) $('.content').css('margin-bottom', height);
		else $('.content').css('margin-bottom', '');
	});


});