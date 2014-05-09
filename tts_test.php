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
require_once('../../config.php');

global $CFG, $OUTPUT, $PAGE;

//Replace get_context_instance by the class for moodle 2.6+
if(class_exists('context_module'))
{
    $context = context_system::instance();
}
else
{
    $context = get_context_instance(CONTEXT_SYSTEM, 0);
}

require_login(1);

$PAGE->set_title('TTS Test Page');
$PAGE->set_url('/blocks/tts/tts_test.php');
$PAGE->navbar->add('TTS Test Page');

echo $OUTPUT->header();


$testgoogle = @get_headers("http://translate.google.com/translate_tts");
$testmicrosoft = @get_headers("http://api.microsofttranslator.com/V2/http.svc/Speak");

if($testgoogle === false)
{
    echo 'Fail on retreiving information from Google server. Please check your server connectivity. (Apache server must have a gateway).<br/>';
}
else
{
    echo 'There is no problem to access to the Google server.<br/>';
}
if($testmicrosoft === false)
{
    echo 'Fail on retreiving information from Microsoft server. Please check your server connectivity. (Apache server must have a gateway).<br/>';
}
else
{
    echo 'There is no problem to access to the Microsoft server.<br/>';
}

echo $OUTPUT->footer();
?>