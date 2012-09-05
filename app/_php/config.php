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
require_once('../../../../config.php');
require_once('service_lib.php');
global $CFG;
/*
  Define constants.  Some of these are used to configure the JSON app
 */

require_once('../../settings_base.php');
$numSelection = numSelection();
$volume = volumeSelection();
$services = serviceSelection();
$language = languageSelection();

//error message if null text is sent to the TTS fetcher.
define("ERROR_MESSAGE", "sound error");

//increase or decrease these based on how much load your server can handle.
define("MAX_REQUESTS", $numSelection[$CFG->block_tts_max_requests]);
define("MAX_ERRORS", $numSelection[$CFG->block_tts_max_errors]);
define("MAX_FAT_FETCH_ATTEMPTS", $numSelection[$CFG->block_tts_max_fat_fetch_attempts]);
//system paths
define("HOST_URL", $CFG->wwwroot);
define("FETCHER_PATH", HOST_URL . "/blocks/tts/app/_php/");
define("CACHE_PATH", "$CFG->dataroot/sound_cache"); //NOTE: HARDCODED VALUE IN CRON function ... It has issues with requiring this config
define("CACHE_DIRECTORY_NAME", "/mp3_tts/");
define("CACHE_BASE_URL", HOST_URL . "/blocks/tts/file.php/sound_cache");
define("SM_SWF_URL", HOST_URL . "/blocks/tts/app/_scripts/_swf/");
define("SM_SCRIPT_URL", HOST_URL . "/blocks/tts/app/_scripts/soundmanager2.js");
define("ERROR_SOUND_URL", HOST_URL . "/blocks/tts/app/_scripts/_swf/null.mp3");
define("FETCH_URL", HOST_URL . "/blocks/tts/app/_php/fetcher.php");
define("ERROR_REPORT_URL", HOST_URL . "/blocks/tts/app/_php/error_reporter.php");
define("ERROR_REPORT_PATH", "$CFG->dataroot/sound_cache/error.log");
define("FAT_FETCH_URL", HOST_URL . "/blocks/tts/app/_php/fat_fetcher.php");
define("RELATIVE_PATH_TO_MOODLE_CONFIG", "../../../../config.php");

/* * *Sound Manager 2 Configuration** */
define("SM_STARTING_VOLUME", $volume[$CFG->block_tts_sm_starting_volume]);

/* * *span configuration** */
//these classes are added to all of the spans that will be played or highlited.  make sure that the name does not in use on your page
define("SPAN_CLASS", "phrase");
define("HIGHLITE_CLASS", "highlite");
define("LOADING_CLASS", "loading");

//SELECTOR is the tag or class or id in the DOM under which all content will be rendered into spans to be read aloud. see jquery
define("SELECTOR", "#region-main");

//NOT is the tag or class or id in the DOM under which all content will be excluded from the read aloud.  see jquery
define("NOT", "#left-column , #right-column, .accesshide, input, style, noscript,.subscription, .forumheaderlist, .modified,.tabtree,#ytplayertime0,#xhtml,.forummode,.survey_text_area");

/* * *service variables** */
$supportedServices = array(
    "google" =>
    array(
        "default_voice" => "moodle_lang",
        "capacity" => 100,
        "voices" => array(
            //lang(current_language()) => lang(current_language())
            'de' => 'de',
            'en' => 'en',
            'es' => 'es',
            'fr' => 'fr',
            'it' => 'it'
        )
    ),
    "microsoft" =>
    array(
        "default_voice" => "moodle_lang",
        "capacity" => 100,
        "voices" => array(
            lang(current_language()) => lang(current_language())
        )
    )
);

define("DEFAULT_SERVICE", $services[$CFG->block_tts_default_service]);
//define("DEFAULT_SERVICE" , "microsoft");	
//make sure this is a member of $supportedServices[service][voices]
define("DEFAULT_SERVICE_VOICE", lang(current_language()));
?>