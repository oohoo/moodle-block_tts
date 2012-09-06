<?php

/**
 * *************************************************************************
 * *                    OOHOO - TTS - Text To Speech                      **
 * *************************************************************************
 * @package     block                                                     **
 * @subpackage  TTS                                                       **
 * @name        TTS                                                       **
 * @copyright   oohoo.biz                                                 **
 * @link        http://oohoo.biz                                          **
 * @author      Ryan Thomas (Original Author)                             **
 * @author      Dustin Durand                                             **
 * @author      Nicolas Bretin                                            **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */
require_once('app/_php/service_lib.php');

/**
 * Class for the content of the block TTS 
 */
class block_tts extends block_base
{

    /**
     * Init the block 
     */
    function init()
    {
        $this->title = get_string('pluginname', 'block_tts');
    }

    /**
     * Specify if the instance allow multiple blocks
     * @return boolean 
     */
    function instance_allow_multiple()
    {
        return false;
    }

    /**
     * Specify if the instance allow config
     * @return boolean 
     */
    function instance_allow_config()
    {
        return true;
    }

    /**
     * Specify if the block has a config
     * @return boolean 
     */
    function has_config()
    {
        return true;
    }

    /**
     * Specify where the block is allowed 
     * @return array The array of page formats
     */
    function applicable_formats()
    {
        return array(
            'site-index' => false,
            'course-view' => true,
            'mod' => true
        );
    }

    /**
     * Specialization function
     */
    function specialization()
    {

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title))
        {
            $this->title = get_string('pluginname', 'block_tts');
        }
        else
        {
            $this->title = $this->config->title;
        }
    }

    /**
     * Get the content and return it in HTML
     * @global stdClass $CFG
     * @global stdClass $COURSE
     * @global moodle_page $PAGE
     * @global core_renderer $OUTPUT
     * @return string Return the formatted content 
     */
    function get_content()
    {
        global $CFG, $COURSE, $PAGE;

        //Get global/admin tts configs
        require_once('settings_base.php');
        $numSelection = numSelection();
        $volume = volumeSelection();
        $services = serviceSelection();
        //$language = languageSelection();

        $course = $this->page->course;
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        $this->content = new stdClass;

        $ttsAppURL = $CFG->wwwroot . '/blocks/tts/app/';

        $this->content->text = '<script type="text/javascript">
            
var ttsAppURL = "' . $ttsAppURL . '";
var ttsService = "' . $services[$CFG->block_tts_default_service] . '";   
var ttsVolume =  "' . $volume[$CFG->block_tts_sm_starting_volume] . '";
var course =  ' . $COURSE->id . ';  
var ttsLang =  "' . lang(current_language()) . '";

var ttsMaxReq =  "' . $numSelection[$CFG->block_tts_max_requests] . '";    
var ttsMaxErr =  "' . $numSelection[$CFG->block_tts_max_errors] . '";    
var ttsFatFet =  "' . $numSelection[$CFG->block_tts_max_fat_fetch_attempts] . '";

var ttsImgUrl = "' . $ttsAppURL . '_images/";
</script>';

        //$PAGE->requires->js('/blocks/tts/tts.js');
        $PAGE->requires->css('/blocks/tts/app/_css/player.css');
        $PAGE->requires->css('/blocks/tts/app/_css/jquery.ui.core.css');
        $PAGE->requires->css('/blocks/tts/app/_css/jquery.ui.slider.css');
        $PAGE->requires->css('/blocks/tts/app/_css/jquery.ui.theme.css');


        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery-1.6.1.min.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery.ui.core.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery.ui.widget.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery.ui.mouse.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery.ui.slider.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/json2.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/jquery.store.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/md4.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/tts.js');
        $PAGE->requires->js('/blocks/tts/app/_scripts/tts_init.js');



        $ttsControls = '<div id="tts_controls"><ul style="padding:5px;" id="tts_control_list">';
        $ttsControls .= '<li style="padding-right:5px;"><a href="#" id="tts_play"><img alt="play button: text to speech" src="' . $ttsAppURL . '_images/play.png" class="play"/></a></li>';
        $ttsControls .= '<li style="padding-right:20px;"><a href="#" id="tts_pause"><img alt="pause button: text to speech" src="' . $ttsAppURL . '_images/pause.png" class="pause" /></a></li>';
        $ttsControls .= '<li style="padding-right:5px;"><a href="#" id="tts_skip_backward"><img alt="skip backward button: text to speech" class="backward" src="' . $ttsAppURL . '_images/backward.png"/></a></li>';
        $ttsControls .= '<li><a href="#" id="tts_skip_forward"><img alt="skip forward button: text to speech" src="' . $ttsAppURL . '_images/forward.png" class="forward"/></a></li>';

        $ttsControls .= '<li style="display:inherit">
                <a href="#" id="tts_volume_down"><img src="' . $ttsAppURL . '_images/volumedown.png" class="volumedown"/></a>
                <div id="tts_volume_slider"></div>
                <a href="#" id="tts_volume_up"><img src="' . $ttsAppURL . '_images/volumeup.png" class="volumeup"/></a>
                <a href="#" id="tts_mute"><img alt="mute button: text to speech" src="' . $ttsAppURL . '_images/mute.png" class="mute"/></a>             
            </li>';

        //$ttsControls .= '<li><a href="#" id="tts_volume_down"><img alt="decrease volume button: text to speech" src="'.$ttsAppURL.'_images/volumedown.png" height="15" width="18" /></a></li>';
        //$ttsControls .= '<li><a href="#" id="tts_volume_up"><img alt="increase volume button: text to speech" src="'.$ttsAppURL.'_images/volumeup.png" height="15" width="18" /></a></li>';
        //$ttsControls .= '<li><a href="#" id="tts_mute"><img alt="mute button: text to speech" src="'.$ttsAppURL.'_images/mute.png" height="15" width="18" /></a></li></ul>';

        $ttsControls .= '</ul>';

        $this->content->text .= $ttsControls;


        if (has_capability('moodle/course:manageactivities', $context))
        {
            $this->content->footer = '<a href="' . $CFG->wwwroot . '/blocks/tts/lexicon.php?courseid=' . $course->id . '">Lexicon</a>';
            global $OUTPUT;
            $this->content->footer .= $OUTPUT->help_icon('lexicon', 'block_tts');
        }
        $this->content->text .= '</div>';

        return $this->content;
    }

    /**
     * Specify if the header must be hidden
     * @return boolean 
     */
    function hide_header()
    {
        return false;
    }

    /**
     * Return the preferred width for the block
     * @return int 
     */
    function preferred_width()
    {
        // The preferred value is in pixels
        return 230;
    }

    /**
     * This serves as a cleanup for files that have become obselete thanks to a new lexicon entry.
     *   
     * I've decided not to write as a loop or recursive(which would elimate alot of repetitive code/functions), but as ugly repeating functions
     *  This allows a person to see the progression down the filesystem heirchary
     *      CACHEROOT: COURSE->mp3_tts->SERVICE->LANGUAGE->files
     * 
     *  The files are always structured as base___lastmodified. By sorting them we can determine the latest version, and delete the obselete
     * ones.
     *
     * @global stdClass $CFG
     * @return boolean  
     */
    function cron()
    {
        global $CFG;

        //NOTE: This setting needs to be changed WITH the config.php DEFINE in app/_php/.
        define("CACHE_PATH", "$CFG->dataroot/sound_cache");

        mtrace('Cleaning old tts audio...');

        $path = CACHE_PATH . '/';

        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {

                if ($file == '.' || $file == '..')
                    continue;

                if (is_dir(CACHE_PATH . '/' . $file))
                {
                    $this->cron_mp3_tts($path, $file);
                }
            }
            closedir($handle);
        }

        return true;
    }

    /**
     * Cron function
     * Could be simplified but hardcoded
     * @param string $path The path to the mp3
     * @param string $dir The dir path
     */
    function cron_mp3_tts($path, $dir)
    {

        $path = $path . $dir . '/';

        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {

                if ($file != 'mp3_tts')
                    continue;

                if (is_dir($path . $file))
                {
                    $this->cron_service($path, $file);
                }
            }
            closedir($handle);
        }
    }

    /**
     * Cron function
     * @param string $path The path to the mp3
     * @param string $dir The dir path
     */
    function cron_service($path, $dir)
    {

        $path = $path . $dir . '/';

        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {

                if ($file == '.' || $file == '..')
                    continue;

                if (is_dir($path . $file))
                {
                    $this->cron_language($path, $file);
                }
            }
            closedir($handle);
        }
    }

    /**
     * Cron function
     * @param string $path The path to the mp3
     * @param string $dir The dir path
     */
    function cron_language($path, $dir)
    {

        $path = $path . $dir . '/';


        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {

                if ($file == '.' || $file == '..')
                    continue;

                if (is_dir($path . $file))
                {
                    $this->cron_audio($path, $file);
                }
            }
            closedir($handle);
        }
    }

    /**
     * Cron function
     * @param string $path The path to the mp3
     * @param string $dir The dir path
     */
    function cron_audio($path, $dir)
    {

        $path = $path . $dir . '/';
        $audioFiles = array();

        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {

                if ($file == '.' || $file == '..')
                    continue;

                if (is_dir($path . $file))
                {
                    continue;
                }

                $audioFiles[] = $path . $file;
            }
            closedir($handle);

            $this->clean_old_files($audioFiles);
        }
    }

    /**
     * Cron function which delete old files
     * @param string $path The path to the mp3
     * @param string $dir The dir path
     */
    function clean_old_files($audioFiles)
    {

        $baseclean = array();
        $obselete = array();

        sort($audioFiles);

        $lastfile = array('', '');

        foreach ($audioFiles as $file)
        {
            $file = explode('___', $file);

            if (count($file) != 2)
                continue;

            //check if start of file is the same
            //if it is then we know the previous was older aka needs to be deleted since it was sorted
            if ($file[0] == $lastfile[0])
            {
                unlink($lastfile[0] . '___' . $lastfile[1]);
            }

            $lastfile = $file;
        }
    }

}

