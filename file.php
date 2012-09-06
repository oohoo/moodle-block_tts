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
require_login();
$relativepath = get_file_argument('file.php');

// relative path must start with '/', because of backup/restore!!!
if (!$relativepath)
{
    error('No valid arguments supplied or incorrect server configuration');
}
else if ($relativepath{0} != '/')
{
    error('No valid arguments supplied, path does not start with slash!');
}

$directories = explode('/', $relativepath);

//print_object($directories);
//Quick Security Checks
$clean = true;
foreach ($directories as $directory)
{
    $pos = strpos($directory, '..');
    if ($pos !== false)
    {
        $clean = false;
    }
}

//%dir%---/sound_cache/%course%/mp3_tts/google/en/11fb1475647d5679733d89acea1632fc.mp3"
//Security Checks - we are going to use some basic security to try to keep this file
//from being abused. Its far from the fastest script, but this helps

$file = $directories[count($directories) - 1];

$filename = explode('.', $file);
$extention = $filename[count($filename) - 1];

//print $extention;

if ($directories[1] != 'sound_cache')
{
    $clean = false;
}
elseif ($directories[3] != 'mp3_tts')
{
    $clean = false;
}
elseif (!$DB->record_exists('course', array('id' => $directories[2])))
{
    $clean = false;
}
elseif ($extention != 'mp3' && $extention != 'wav')
{
    $clean = false;
}

$filePath = $CFG->dataroot . $relativepath;

// check that file exists
if (!$clean || !file_exists($filePath))
{
    not_found();
}

session_write_close(); // unlock session during fileserving
send_file($filePath, $file, $extention);

/**
 * Send a header 404 page not found to the user 
 */
function not_found()
{
    header('HTTP/1.0 404 not found');
}

/**
 * Read file
 * @param string $filename The string file name
 * @param boolean $retbytes
 * @return boolean 
 */
function readfile_chunked($filename, $retbytes = true)
{
    $chunksize = 1 * (1024 * 1024); // 1MB chunks - must be less than 2MB!
    $buffer = '';
    $cnt = 0;
    $handle = fopen($filename, 'rb');
    if ($handle === false)
    {
        return false;
    }

    while (!feof($handle))
    {
        @set_time_limit(60 * 60); //reset time limit to 60 min - should be enough for 1 MB chunk
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        flush();
        if ($retbytes)
        {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status)
    {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}

/**
 * Send file to download to the user
 * @global stdClass $CFG
 * @global stdClass $COURSE
 * @global stdClass $SESSION
 * @param string $path      The path of the file
 * @param string $filename  The file name
 * @param string $extension The file extension
 */
function send_file($path, $filename, $extension)
{
    global $CFG, $COURSE, $SESSION;

    //print $path . " " . $filename . " " . $extension;exit();

    $filesize = filesize($path);

    //IE compatibiltiy HACK!
    if (ini_get('zlib.output_compression'))
    {
        ini_set('zlib.output_compression', 'Off');
    }

    //try to disable automatic sid rewrite in cookieless mode
    @ini_set("session.use_trans_sid", "false");

    @header('Content-Disposition: inline; filename="' . $filename . '"');

    $lifetime = $lifetime = 86400;
    @header('Cache-Control: max-age=' . $lifetime);
    @header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    @header('Pragma: ');

    // Just send it out raw
    @header('Content-Length: ' . $filesize);
    @header('Content-Type: ' . $mimetype);
    while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
    readfile_chunked($path);

    die; //no more chars to output!!!
}

?>
