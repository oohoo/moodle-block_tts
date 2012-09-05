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
//location to tts_config should be supplied as a parameter in client side script
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
//this file is for configuring the javascript portion of the application.
require_once('config.php');

$currentVoice = optional_param('voice', null, PARAM_TEXT);
$currentService = optional_param('service', null, PARAM_TEXT);

$JSONobj = new StdClass();

$JSONobj->TTS = new StdClass();
$JSONobj->TTS->SM_SCRIPT_URL = SM_SCRIPT_URL;
$JSONobj->TTS->SM_SWF_URL = SM_SWF_URL;
$JSONobj->TTS->SM_STARTING_VOLUME = SM_STARTING_VOLUME;

$JSONobj->prefetch = new stdClass();
$JSONobj->prefetch->MAX_ERRORS = MAX_ERRORS;
$JSONobj->prefetch->MAX_REQUESTS = MAX_REQUESTS;
$JSONobj->prefetch->MAX_FAT_FETCH_ATTEMPTS = MAX_FAT_FETCH_ATTEMPTS;
$JSONobj->prefetch->FETCH_URL = FETCH_URL;
$JSONobj->prefetch->FAT_FETCH_URL = FAT_FETCH_URL;
$JSONobj->prefetch->ERROR_REPORT_URL = ERROR_REPORT_URL;
$JSONobj->prefetch->ERROR_SOUND_URL = ERROR_SOUND_URL;
$JSONobj->prefetch->NOT_PREFETCHED = 0;
$JSONobj->prefetch->PREFETCH_IN_PROGRESS = 1;
$JSONobj->prefetch->SERVER_WAITING_FOR_MP3 = 2;
$JSONobj->prefetch->SERVER_HAS_MP3 = 3;
$JSONobj->prefetch->SPAN_HAS_UNLOADED_SOUND = 4;
$JSONobj->prefetch->SPAN_HAS_LOADED_SOUND = 5;
$JSONobj->prefetch->SPAN_THREW_ERROR = 6;

$JSONobj->spans = new StdClass();
$JSONobj->spans->currentSpan = 0;
$JSONobj->spans->previousSpan = -1;
$JSONobj->spans->nextSpanToPlay = 0;
$JSONobj->spans->spanClass = SPAN_CLASS;
$JSONobj->spans->notWaitingToPlayCurrent = true;
$JSONobj->spans->playingSpans = 0;
$JSONobj->spans->maxSimultaneousSpans = 1;
$JSONobj->spans->firstPlay = true;
$JSONobj->spans->highliteClass = HIGHLITE_CLASS;
$JSONobj->spans->loadingClass = LOADING_CLASS;
$JSONobj->spans->selector = SELECTOR;
$JSONobj->spans->not = NOT;
$JSONobj->spans->CurrentSpanCapacity = 0;

$JSONobj->services = new StdClass();
$JSONobj->services->currentService = DEFAULT_SERVICE;
$JSONobj->services->currentVoice = DEFAULT_SERVICE_VOICE;
$JSONobj->services->newVoice = true;
$JSONobj->services->newService = true;


if ($currentVoice != '')
{
    $JSONobj->services->currentVoice = $currentVoice;
}
if ($currentService != '')
{
    $JSONobj->services->currentService = $currentService;
}

$serviceList = array();
foreach ($supportedServices as $key => $value)
{
    array_push($serviceList, $key);
    $JSONobj->services->$key = new StdClass();
    $JSONobj->services->$key->defaultVoice = $supportedServices[$key]['default_voice'];
    $JSONobj->services->$key->capacity = $supportedServices[$key]['capacity'];
    $service_voices = array();
    foreach ($supportedServices[$key]['voices'] as $i => $voice_value)
    {
        array_push($service_voices, $voice_value);
    }
    $JSONobj->services->$key->voices = $service_voices;
}

//send response
echo json_encode($JSONobj);
?>