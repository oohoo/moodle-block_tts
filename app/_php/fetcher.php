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
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
//include the php config fie
require_once('config.php');

//scrape $_REQUEST data for variables to be manipulated
$state = isset($_REQUEST['state']) ? (int) htmlentities($_REQUEST['state']) : -1;
$span = isset($_REQUEST['span']) ? (int) htmlentities($_REQUEST['span']) : -1;
$course = isset($_REQUEST['course']) ? (int) htmlentities($_REQUEST['course']) : -1;
$service = isset($_REQUEST['service']) ? htmlentities($_REQUEST['service']) : DEFAULT_SERVICE;
$text = isset($_REQUEST['text']) ? htmlentities($_REQUEST['text']) : ERROR_MESSAGE;
$mp3file = '';

// if REQUEST data is reasonable, fetch mp3 for it 
if ($state !== -1 && $span !== -1 && $text !== ERROR_MESSAGE && $course !== -1)
{

    //checking if requested service (or default service) is in the list of supported tts services
    if (array_key_exists($service, $supportedServices))
    {

        $voice = isset($_REQUEST['voice']) ? htmlentities($_REQUEST['voice']) : DEFAULT_SERVICE_VOICE;

        //checking if requested voice (or default voice) is in the list of supported voices for the selected tts service
        if (array_key_exists($voice, $supportedServices[$service]['voices']))
        {

            require_once('_services/general_tts_config.php');
            require_once('_services/' . $service . '_tts_config.php');

            $service_fetcher_name = ucwords($service) . 'Fetch';
            $service_fetcher = new $service_fetcher_name(
                            $supportedServices[$service]['voices'][$voice],
                            CACHE_PATH . '/' . $course . CACHE_DIRECTORY_NAME . $service . '/' . $supportedServices[$service]['voices'][$voice] . '/',
                            CACHE_BASE_URL . '/' . $course . CACHE_DIRECTORY_NAME . $service . '/' . $supportedServices[$service]['voices'][$voice] . '/',
                            $text,
                            ERROR_MESSAGE);

            //if the prefetcher can locate or create a folder for caching mp3s		
            if (!$service_fetcher->badInit)
            {

                $error = false;
                if ($service_fetcher->checkMP3Exists())
                {
                    $state = 3;
                    $mp3file = $service_fetcher->getMP3URL();
                    unset($service_fetcher);
                }
                else
                {
                    if ($state == 1)
                    {
                        if ($service_fetcher->requestMP3FromService())
                        {
                            $state = 2;
                        }
                    }
                    unset($service_fetcher);
                }
            }
        }
        else
        {
            $error = true;
        }
    }
    else
    {
        $error = true;
    }
}
else
{
    $error = true;
}
if ($mp3file == '')
{
    $error = true;
}

$JSONobj = new StdClass();
$JSONobj->state = $state;
$JSONobj->span = $span;
$JSONobj->file = $mp3file;
$JSONobj->error = $error;

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($JSONobj);
unset($JSONobj);
?>