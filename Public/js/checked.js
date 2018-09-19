$(document).ready(function(){
		$("#select").click( function () {
			$check = $(".check");
			if($check.attr("checked") != "checked"){
				$check.attr("checked",true);
			}else{
				$check.attr("checked",false);
			}
		});
		
		$(".auth_model_choose").click(function(){
			var domid = this.id;
			$("."+domid).each(function(index){ 
				$(this).attr("checked",true);
			}); 
		});
		
		$(".auth_model_choose_cancel").click(function(){
			var domid = this.id;
			$("."+domid).each(function(index){ 
				$(this).attr("checked",false);
			}); 
		});
		$(".auth_model_choose_black").click(function(){
			var domid = this.id;
			$("."+domid).each(function(index){ 
				if($(this).attr("checked")){
					$(this).attr("checked",false);
				}else{
					$(this).attr("checked",true);
				}
			}); 
		});
		
		
		
		
	});