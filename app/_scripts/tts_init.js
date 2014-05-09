//check if theme header can call moodle code to check user preferences before deploying this script.
/* 
This is the controller script.  It scrapes the body tag to determine whether the page is a main course view
or module (resource/assignment/forum/etc).  It creates a UI and attaches events from tts.js to it.  It then calls
tts.init.initialize
*/

$(document).ready(function() {
    //Add Events to the botton for effects
    $('#tts_control_list a').each(function(){
        $(this).hover(function(){
            var img = $('img', this);
            img.attr('src', ttsImgUrl+img.attr('class')+'_hover.png');
        },function(){
            var img = $('img', this);
            img.attr('src', ttsImgUrl+img.attr('class')+'.png');
        });
        $(this).mousedown(function(){
            var img = $('img', this);
            img.attr('src', ttsImgUrl+img.attr('class')+'_click.png');
        });
        $(this).mouseup(function(){
            var img = $('img', this);
            img.attr('src', ttsImgUrl+img.attr('class')+'.png');
        });
    });



    //do not initialize on non-view pages.  Excludes pages like participation reports and grades.
    if(typeof jQuery === 'undefined' || jQuery("body").attr("id").indexOf("-view") == -1){
        return;
    }

    //do not initialize on pages without breadcrumb
    if ($(".breadcrumb").length == 0){
        return; 
    }

    //initialize course, use Moodle 1.9 bode class of course-# to extract the course number.  This is used later.
    var course = 1;
    $.each($("body").attr("class").split(" "),function(i,value){
        if(value.indexOf("course-") != -1){
            course = parseInt(value.replace("course-",""));
        }
    });

    //course is only used to store mp3s in the correct location and create the potential for access control.  This could be stripped out.
    //blacklisted courses  do not run TTS on site course 1, sandbox or staff instruction course.
    if (course <= 1){
        return;
    }

    //check if a module is loaded (resource, forum, etc.
    var module = false;
    $.each($("body").attr("id").split("-"),function(i,value){
        if(value.indexOf("mod") != -1){
            module = true;
        }
    });
    //Always a module
    module = true;

    var ttsControls = '';

    if (module){
        ttsControls += '<div id="tts_init_message"></div>';
        ttsControls += '<div id="tts_loading_screen"></div>';

    }
    else{
        ttsControls += '<div id="tts_init_message"></div>';
    }

    var body=document.body;

    //make UI first set of elements after body.
    $("#tts_controls").append(ttsControls);
    if (!module){
        //play click event required to start up TTS on main course page.
        $("#tts_play").click(function(event){
            event.preventDefault();
            tts.init.initialize(initArgs,initSuccess,initFail);
            $(this).unbind('click');
            $(this).click(function(event){event.preventDefault();tts.UI.play();});
        });
    }

    //init args are passed to tts.js function tts.init.initialize
    var initArgs = {
        url:ttsAppURL+'_php/tts_config.php?courseid='+course+'&service='+ttsService+'&voice='+ttsLang,
        //service:'microsoft',
        service:ttsService,
        voice:ttsLang,
        course:course
    };

    var initSuccess = function(){
        //start floating and attach events to UI
        $("#tts_init_message").empty();
        $("#tts_loading_screen").fadeOut();


        if (module){
            $("#tts_play").click(function(event){event.preventDefault();tts.UI.play();});
        }
        //actually attaching events to elements in the UI string. (this is the controller in a MVC architecture)
        $("#tts_pause").click(function(event){event.preventDefault();tts.UI.pause();});
        $("#tts_skip_forward").click(function(event){event.preventDefault();tts.UI.skipForward();});
        $("#tts_skip_backward").click(function(event){event.preventDefault();tts.UI.skipBack();});
        $("#tts_volume_up").click(function(event){event.preventDefault();tts.UI.volumeUp();});
        $("#tts_volume_down").click(function(event){event.preventDefault();tts.UI.volumeDown();});
        $("#tts_mute").click(function(event){event.preventDefault();tts.UI.toggleMute();});


        $( "#tts_volume_slider" ).slider({
            min: 0,
            max: 100,
            value: ttsVolume,   
            slide: function(event){;tts.UI.setVolume();}
        });

    };
    // variable was included when fail conditions were common.  This is a depreciated mechanism allowing the UI to report failiures.
    //empty function is still passed as a parameter to tts.init.initialize.
    var initFail = function(){
        $("#tts_init_message").html('Player Timeout');
    };

    if(module){
        tts.init.initialize(initArgs,initSuccess,initFail);
    }
});