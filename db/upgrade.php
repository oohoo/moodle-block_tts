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
defined('MOODLE_INTERNAL') || die();

/**
 * This function is run when the plugin have to be updated
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @param int $oldversion The older version of the plugin installed on the moodle
 * @return boolean True if the update passed successfully
 */
function xmldb_block_tts_upgrade($oldversion)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011090604)
    {
        // Define field lastmodified to be added to tts_lexicon
        $table = new xmldb_table('tts_lexicon');
        $field = new xmldb_field('lastmodified', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'courseid');

        // Conditionally launch add field lastmodified
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // tts savepoint reached
        upgrade_block_savepoint(true, 2011090604, 'tts');
    }

    if ($oldversion < 2011120800)
    {

        // + Modification on the google translation to allow more than english to speech
        // + Add French labels translation
        // + Correction on google_tts_config for specials chars like accents
        // tts savepoint reached
        upgrade_block_savepoint(true, 2011120800, 'tts');
    }

    return true;
}

?>
