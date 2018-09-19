$(function(){

    $(document).click(function(){//用于点击页面任何地方都隐藏id="lang_ul"的ul
        $("#lang_ul").slideUp();
    });
    $("#check_lang").click(function(e){//点击按钮用来显示和隐藏id="lang_ul"的ul
        e.stopPropagation();  //用来阻止当点击按钮时  不执行$(document).click函数;
        $("#lang_ul").slideToggle();
    });
    $("#lang_ul li").click(function(){
        $("#g_current_lang").text($(this).text());
        $(this).find("em").show();
        $(this).siblings("li").find("em").hide();
    });
    $(document).click(function(){
        $("#lang_ul2").slideUp();
    });	
});