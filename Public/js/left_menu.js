$(function(){
	$('body').mousemove(function(e) {
        var mouseLeft=e.pageX;
        if(mouseLeft=="0"){
            $("#g_slider_menu").animate({left:"0px"},100);
        }
    });
    $("#g_slider_menu").mouseleave(function(){
        $("#g_slider_menu").animate({left:"-139px"});
    });

    $("#g_switch_menu").mousemove(function(){
        $("#g_slider_menu").animate({left:"0px"},100);
    });

    $("#g_switch_menu").mouseleave(function(){
        $("#g_slider_menu").animate({left:"-139px"});
    });
});
