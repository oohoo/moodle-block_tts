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
cron();

//Function call to moodle
//Starts the traversing of the filesystem at the sound_cache root
//The function loops through all courses making a call to further travserse each course tree 
function cron()
{
    global $CFG;
    require_once('config.php');

    $path = CACHE_PATH . '/';

    if ($handle = opendir($path))
    {
        while (false !== ($file = readdir($handle)))
        {

            if ($file == '.' || $file == '..')
                continue;

            echo $file;

            if (is_dir(CACHE_PATH . '/' . $file))
            {
                echo '?';
                cron_mp3_tts($path, $file);
            }
        }
        closedir($handle);
    }
}

//Could be simplified but hardcoded
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
                cron_service($path, $file);
            }
        }
        closedir($handle);
    }
}

function cron_service($path, $dir)
{

    $path = $path . $dir . '/';

    print $path . '<br/>';

    if ($handle = opendir($path))
    {
        while (false !== ($file = readdir($handle)))
        {

            if ($file == '.' || $file == '..')
                continue;

            if (is_dir($path . $file))
            {
                cron_language($path, $file);
            }
        }
        closedir($handle);
    }
}

function cron_language($path, $dir)
{

    $path = $path . $dir . '/';

    print $path . '<br/>';

    if ($handle = opendir($path))
    {
        while (false !== ($file = readdir($handle)))
        {

            if ($file == '.' || $file == '..')
                continue;

            if (is_dir($path . $file))
            {
                cron_audio($path, $file);
            }
        }
        closedir($handle);
    }
}

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

        clean_old_files($audioFiles);
    }
}

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

?>
