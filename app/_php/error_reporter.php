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


$errors = isset($_POST['errors']) ? (int) htmlentities($_POST['errors']) : -1;
$thisURL = isset($_POST['thisURL']) ? htmlentities($_POST['thisURL']) : 'No URL specified';

$log = '';

if ($errors > 0)
{

    //collect the rest of the post
    require_once(RELATIVE_PATH_TO_MOODLE_CONFIG);
    //get user name
    global $USER;

    $uname = $USER->firstname . " " . $USER->lastname;
    $today = date("F j, Y, g:i a");
    $log .= "\n" . $today . "  -  " . $uname . "\n";

    for ($i = 0; $i < $errors; $i++)
    {
        if (isset($_POST['file_' . $i]) && isset($_POST['text_' . $i]))
        {
            $log .= htmlentities($_POST['file_' . $i] . ' | ' . $_POST['text_' . $i]) . "\n";
        }
    }

    $log .= "----------------------\n";

    $myFile = ERROR_REPORT_PATH;
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $log);
    fclose($fh);
}

$JSONobj = new StdClass();
$JSONobj->done = true;
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($JSONobj);
unset($JSONobj);
?>