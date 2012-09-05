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

//scrape $_POST data for variables to be manipulated

$span_count = isset($_POST['span_count']) ? (int) htmlentities($_POST['span_count']) : -1;
$course = isset($_POST['course']) ? (int) htmlentities($_POST['course']) : -1;
$service = isset($_POST['service']) ? htmlentities($_POST['service']) : DEFAULT_SERVICE;

$spans = array();
$results = array();
$debug = '';
if ($span_count > 0 && $course !== -1)
{
    //collect the rest of the post
    $debug .= 'counting \n';
    for ($i = 0; $i < $span_count; $i++)
    {
        if (isset($_POST['span_' . $i]) && isset($_POST['text_' . $i]))
        {
            $span = new StdClass();
            $span->id = (int) htmlentities($_POST['span_' . $i]);
            $span->text = htmlentities($_POST['text_' . $i]);
            array_push($spans, $span);
        }
    }
}

if (count($spans) > 0 && $course !== -1)
{
    $debug .= ' counting spans \n';
    //checking if requested service (or default service) is in the list of supported tts services
    if (array_key_exists($service, $supportedServices))
    {
        $debug .= ' service ok \n';
        $voice = isset($_POST['voice']) ? htmlentities($_POST['voice']) : DEFAULT_SERVICE_VOICE;

        //checking if requested voice (or default voice) is in the list of supported voices for the selected tts service
        if (array_key_exists($voice, $supportedServices[$service]['voices']))
        {
            $debug .= ' voice ok \n';
            require_once('_fat_services/general_tts_config.php');
            require_once('_fat_services/' . $service . '_tts_config.php');
            //use variable variables to get child object file for server-side service.
            $service_fetcher_name = ucwords($service) . 'Fetch';
            //caching file path.  Moodle 1.9 specific.  Use repository in 2.0
            //one way to do it would be to store the mp3 on a server and also write a repository plugin referencing the server.
            //may not need to use repository.  This could be a php service outside of Moodle that the JavaScript hooks into.
            $service_fetcher = new $service_fetcher_name(
                            CACHE_PATH . '/' . $course . CACHE_DIRECTORY_NAME . $service . '/' . $supportedServices[$service]['voices'][$voice] . '/',
                            CACHE_BASE_URL . '/' . $course . CACHE_DIRECTORY_NAME . $service . '/' . $supportedServices[$service]['voices'][$voice] . '/'
                            , $supportedServices[$service]['voices'][$voice]);
            //if the prefetcher can locate or create a folder for caching mp3s		
            if (!$service_fetcher->badInit)
            {
                $debug .= 'service fetcher is working';
                //$counter = 0;
                foreach ($spans as $span)
                {
                    //$counter = $span->id;
                    //print_r($span);
                    $result = $service_fetcher->checkMP3Exists($span->text);
                    //$a is consumed in the AJAX response
                    if ($result->is_mp3)
                    {
                        $a = new StdClass();
                        $a->span = $span->id;
                        $a->url = $result->mp3URL;
                        array_push($results, $a);
                    }
                    else
                    {
                        
                    }
                }
                unset($service_fetcher);
            }
        }
        else
        {
            //init has failed, do nothing.  no point in returning anything
        }
    }
    else
    {
        //something went wrong with the services
    }
}
else
{
    //something went wrong with the post data
}

$JSONobj = new StdClass();
//can eliminate this IF clause if debugging is removed.
if (count($results) > 0)
{
    //$JSONobj->hasResult = true;
    $JSONobj->results = $results;
    //$JSONobj->counter = $counter;
}
else
{
    $JSONobj->results = $results;
    //$JSONobj->hasResult = false;
    //$JSONobj->debug = $debug;
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($JSONobj);
unset($JSONobj);
?>