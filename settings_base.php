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
//Contains base settings arrays

/**
 * Return an array of numbers
 * @return array Return an array of numbers
 */
function numSelection()
{
    $numSelection = array(1, 2, 3, 4, 5, 6);
    return $numSelection;
}

/**
 * Return an array of numbers for the volume
 * @return array Return an array of numbers
 */
function volumeSelection()
{
    for ($i = 100; $i > 0; $i = $i - 5)
    {
        $volume[] = $i;
    }
    return $volume;
}

/**
 * Return an array of services
 * @return array The array of services
 */
function serviceSelection()
{
    $services = array('google', 'microsoft');
    return $services;
}

/**
 * Return the language en function of the differents moodle options
 * @global stdClass $CFG
 * @global stdClass $SESSION
 * @global stdClass $COURSE
 * @global stdClass $USER
 * @return string Return the language code
 */
function languageSelection()
{
    global $CFG, $SESSION, $COURSE, $USER;
    $lang = substr(strtolower((isset($COURSE->lang) ? $COURSE->lang : $CFG->lang)), 0, 2);

    switch ($lang)
    {
        case 'en':
            $lang = 'en';
            break;
        case 'fr':
            $lang = 'fr';
            break;
        default:
            $lang = 'en';
            break;
    }
    $language = $lang;
    return $language;
}

?>
