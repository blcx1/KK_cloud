$(function(){
	$("#g_userinfo").click(function(e){
        e.stopPropagation();
        $("#lang_ul2").slideToggle();
    });
	if($(window).width()<=480) {
        $("#g_username").text("");
        $(".ico-lang-bg").css("margin-left","-6px");
        $(".web-dialog").css({"width":"94%","top":"36%","left":"3%"});
    }
    if(768<=$(window).width()<1024){
        $("#g_username").text("");
    }
});