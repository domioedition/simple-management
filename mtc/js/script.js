$(function(){
	var $template = $('#template');
	// $template.hide();
});


$(function(){
	var $button = $('#addColumn');
	$button.on('click', function(e){
		var $form = $('#formTemplate');
		$lastInput = $("input").last().attr("name");
		$lastInput = $lastInput[$lastInput.length-1];
		$lastInput++;
		var field = "field"+$lastInput;
		console.log(field);
		var tag = "<input type=\"text\" name="+field+"><br>";
		$form.append(tag);
		e.preventDefault();
	});
});

$(function(){
	var $go = $('#go');
	$go.on('click', function(e){
		var $form = $('#formTemplate');
		$form.submit();		
		e.preventDefault();
	});

});