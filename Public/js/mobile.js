
$(function(){
    

    /*var currentHeight=$(window).height()-70+"px";
     if($("#micloud_right_frame").css("display")=="none") {

     $("#micloud_window").css("height", currentHeight);
     }
     if($("#micloud_left_frame").css("display")=="none") {

     $("#micloud_window").css("height", currentHeight);
     }*/


    $(".sms_list_item,.call_record_list_item").click(function(){
        if(!$(".checkbox").hasClass("active1")&&$(".item_checkbox").css("display")=="none"){
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
    });

    $("#contactListFrame .list_selection_item,.icobtn-edit").click(function(){
        $("#kcloud_right_frame").show();
        $("#kcloud_left_frame").hide();
    });

    $(".icobtn-edit").click(function(){
        $("#contact-detail-padd2").hide();
    });





    $(".icobtn-back,#M-Gen-button-114,#M-Gen-button-10011,font,#M-Gen-button-1221").click(function(){
        $("#kcloud_right_frame").hide();
        $("#kcloud_left_frame").show();
    });
    $(".icobtn-edit").click(function(){
        $("#contact-detail-padd2").hide();
    });
    $("#M-Gen-button-114").click(function(){
        $("#contact-detail-padd2").show();
    });
    $("#contact_more_operate").click(function(){

        if($("#type_selector_0").css("display")=="none"){
            $("#type_selector_0").hide();
        }else{
            $("#type_selector_0").show();
        }
        $(this).find(".btn-tips").hide();
    });

    if($(".blockimportant").hasClass("hidewap")){
        $(".n-footer").hide();
    }


    $(document).click(function(){
        $("#gallery_left ul").slideUp();
    })
    $(".gallery-content").click(function(e){
        e.stopPropagation();
        $("#gallery_left ul").slideDown();
    });
    $("#gallery_left ul li").click(function(){
        $(".gallery-content span").text($(this).find("a").text());
        $("#gallery_left ul").slideUp();
    });
    $("#gallery_right_recycle .image-item").click(function(){
        $("#gallery_right_recycle").hide();
        $("#gallery_right_recycle").addClass("hide");
    });
    $("#add_album_dialog").hide();

        $(".v-gal-down-ico").hide();

    //通讯录页面
    $(".iMIge-image-container").css("width","60%");
});