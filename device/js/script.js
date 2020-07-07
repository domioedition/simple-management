$( window ).load(function() {
  var $div = $('#deviceInfo');
  $div.hide();
});

$(function(){
	var $button = $('#showDeviceInfo');
	$button.on('click', function(e){
		var $div = $('#deviceInfo');
		$div.slideToggle();
		// e.preventDefault();
	});
});


// $(function(){
// 	var $err = $('#action');
// 	$err.height("200px");
// 	$err.text("Errors");
// 	$err.css("background-color","green");
// 	$err.css("color","white");
// 	// console.log($err);
// });