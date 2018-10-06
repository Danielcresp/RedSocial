$(document).ready(function(){ 
	
	$(".nick-input").blur(function(){ /*obtinen el campo nick*/
		var nick = this.value;
		
		$.ajax({
			url: URL+'/nick-test', /*concatenar la url*/
			data: {nick: nick},
			type: 'POST',
			success: function(response){ /*verificar si esta en DB*/
				if(response == "used"){
					$(".nick-input").css("border", "1px solid red");
				}else{
					$(".nick-input").css("border", "1px solid green");
				}
			}
		});
		
	});
	
});