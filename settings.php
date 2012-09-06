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
defined('MOODLE_INTERNAL') || die;

require_once('settings_base.php');

if ($ADMIN->fulltree)
{
    $numSelection = numSelection();
    $volume = volumeSelection();
    $services = serviceSelection();
    $language = languageSelection();

    $settings->add(new admin_setting_configselect('block_tts_max_requests', get_string('max_requests', 'block_tts'),
                    get_string('max_requestsdef', 'block_tts'), 0, $numSelection));
    $settings->add(new admin_setting_configselect('block_tts_max_errors', get_string('max_errors', 'block_tts'),
                    get_string('max_errorsdef', 'block_tts'), 2, $numSelection));
    $settings->add(new admin_setting_configselect('block_tts_max_fat_fetch_attempts', get_string('max_fat_fetch_attempts', 'block_tts'),
                    get_string('max_fat_fetch_attemptsdef', 'block_tts'), 1, $numSelection));
    $settings->add(new admin_setting_configselect('block_tts_sm_starting_volume', get_string('sm_starting_volume', 'block_tts'),
                    get_string('sm_starting_volumedef', 'block_tts'), 5, $volume));
    $settings->add(new admin_setting_configselect('block_tts_default_service', get_string('default_service', 'block_tts'),
                    get_string('default_servicedef', 'block_tts'), 1, $services));
}

