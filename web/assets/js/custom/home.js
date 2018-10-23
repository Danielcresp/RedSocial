$(document).ready(function() {

	$("#option").unbind("click").click(function(){
	$('#menu').addClass('active');
	});

/*	$("#option").unbind("click").click(function(){
		$('#menu').removeClass('active');

	});*/

	var ias = jQuery.ias({
		container: '.box-content',
		item: '.posts-section',
		pagination: '.pagination',
		next: '.pagination .next_link',
		triggerPageThreshold: 5
	});
	
	ias.extension(new IASTriggerExtension({
		text: 'Ver más publicaciones',
		offset: 3
	}));
	
	ias.extension(new IASSpinnerExtension({
		src: URL+'/../assets/images/ajax-loader.gif'
	}));
	
	ias.extension(new IASNoneLeftExtension({
		text: 'No hay más publicaciones'
	}));
	
	ias.on('ready', function(event){
		buttons();
	});
	
	ias.on('rendered', function(event){
		buttons();
	});

});

function buttons(){
	$(".btn-img").unbind("click").click(function(){
		$(this).parent().find('.pub-image').fadeToggle();
	});
	
	$(".btn-delete-pub").unbind('click').click(function(){
		/*$(this).parent().find('.ed-opts').addClass('hidden');*/
		$('#post').addClass('hidden');
		
    $.ajax({
			url: URL+'/publication/remove/'+$(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});
	
	$(".btn-start").unbind('click').click(function(){
		$(this).addClass("hidden");
		$(this).parent().find('.btn-unstart').removeClass("hidden");
		
		$.ajax({
			url: URL+'/like/'+$(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});
	
	/*$(".btn-unlike").unbind('click').click(function(){
		$(this).addClass("hidden");
		$(this).parent().find('.btn-like').removeClass("hidden");
		
		$.ajax({
			url: URL+'/unlike/'+$(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});*/
}
