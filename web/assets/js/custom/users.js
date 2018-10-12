$(document).ready(function() {

	var ias = jQuery.ias({ /*los iten a paginar son */
		container: '.box-users',
		item: '.user-item',
		pagination: '.pagination',
		next: '.pagination .next_link',
		triggerPageThreshold: 5 /*pide la peticion a ajax cada 5*/
	});
	
	ias.extension(new IASTriggerExtension({  /*en caso de que se pasa 3 paginaciones saldra*/
		text: 'Ver más personas',
		offset: 3
	}));
	
	ias.extension(new IASSpinnerExtension({  /*imagen de carga el efecto*/
		src: URL+'/../assets/images/loader-1.gif' /*direccion del gif*/
	}));
	
	ias.extension(new IASNoneLeftExtension({ /*en caso de que no haya mas registros que mostrar*/
		text: 'No hay más personas'
	}));

});