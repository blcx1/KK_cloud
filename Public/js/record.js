$(function(){

//    //录音机选中
//    $('.record-list .checkbox ').click(function(){
//    	$(this).toggleClass('.checked');
//    	$(this).parent('li').toggleClass('.selected');
//    	
//    });   
    
    //切换
    $("#record_tab_all").click(function(){
        $(".record-container").show();
        $(" .year-area").show();
        $(".record-empty").hide();
    });
    $("#record_tab_call").click(function(){
        $(".record-empty").show();
        $(".record-container").hide();
        $(".year-area").hide();
    });
});