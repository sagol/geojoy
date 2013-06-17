function fnShowProps(obj, func){
    var result = "";
    for (var i in obj) // обращение к свойствам объекта по индексу
        result += "obj." + i + " = " + obj[i] + "<br />";
    
   if(typeof(func) == 'undefined')  func = '';
    var line = document.createElement('span');
    line.innerHTML = func +'<br>'+ result+'<br><br>';
    document.body.appendChild(line);
}


jQuery(function($) {
	$('.hint').each(function(index, elem) {
		var id = elem.id.substr(5);
		
		/*$('#'+id).bind('mouseenter mouseleave', function() {
			$('#hint-'+id).toggleClass('show');
		});*/
		$('#'+id).bind('mouseenter', function() {
			var offset = $(this).offset();
			offset.top -= 30;
			//offset.left +=  20;
			$('#hint-'+id).toggleClass('show');
			$('#hint-'+id).offset(offset);
		});
		$('#'+id).bind('mouseleave', function() {
			$('#hint-'+id).toggleClass('show');
		});
	});
});