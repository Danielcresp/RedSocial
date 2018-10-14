$(document).ready(function() {

	/*$(".page-link").unbind("click").click(function(){
			alert("Pagination");

		var ias = jQuery.ias({ los iten a paginar son 
			container: '.box-users',
			item: '.user-item',
			pagination: '.pagination',
			next: '.pagination .next_link',
			triggerPageThreshold: 12 pide la peticion a ajax cada 
		});
	});*/

	var ias = jQuery.ias({ /*los iten a paginar son */
		container: '.box-users',
		item: '.user-item',
		pagination: '.pagination',
		next: '.pagination .next_link',
		triggerPageThreshold: 12 /*pide la peticion a ajax cada 5 */
	});
	
	ias.extension(new IASTriggerExtension({  /*en caso de que se pasa 3 paginaciones saldra */
		text: 'Ver más personas',
		offset: 3
	}));
	
	ias.extension(new IASSpinnerExtension({  /*imagen de carga el efecto */
		src: URL+'/../assets/images/loader-1.gif' /*direccion del gif*/
	}));
	
	ias.extension(new IASNoneLeftExtension({ /*en caso de que no haya mas registros que mostrar*/
		text: 'No hay más personas'
	}));

	ias.on('ready', function(event){
		followButtons();
	});
	
	ias.on('rendered', function(event){
		followButtons();
	});

});

function followButtons(){

	$(".btn-follow").unbind("click").click(function(){
		$(this).addClass("hidden");
		$(this).parent().find(".btn-unfollow").removeClass("hidden");

/*		$(this).addAttr('hidden');*/
		//$(this).parent().find(".btn-unfollow").removeAttr('hidden');
/*		 $id = $(this).attr("data-followed"),
		console.log("Hola humano has accedido al boton ="+ $id);*/
		$.ajax({
			url: URL+'/follow', /*se define la url*/
			type: 'POST', /*se define cual es metodo*/
			data: { /*se envia los datos*/
				followed: $(this).attr("data-followed")}, /*se obtiene el valor del atributo css*/
			success: function(response){ /*se obtine la notificasion*/
			console.log(response); /*se muestra en consola el mensaje de confirmacion*/
			}
		});
	});

	$(".btn-unfollow").unbind("click").click(function(){
		$(this).addClass("hidden");
		$(this).parent().find(".btn-follow").removeClass("hidden");
	/*	$(this).addAttr('hidden');*/
		//$(this).parent().find(".btn-follow").removeAttr('hidden');
		$.ajax({
			url: URL+'/unfollow', /*se define la url*/
			type: 'POST', /*se define cual es metodo*/
			data: { /*se envia los datos*/
				followed: $(this).attr("data-followed")}, /*se obtiene el valor del atributo css*/
			success: function(response){ /*se obtine la notificasion*/
			console.log(response); /*se muestra en consola el mensaje de confirmacion*/
			}
		});
	});
}