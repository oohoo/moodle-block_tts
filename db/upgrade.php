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
    
    if ($oldversion < 2012101800)
    {

        // Define table block_tts to be renamed to NEWNAMEGOESHERE
        $table = new xmldb_table('tts');
        $table2 = new xmldb_table('tts_lexicon');

        // Launch rename table for block_tts
        $dbman->rename_table($table, 'block_tts');
        $dbman->rename_table($table2, 'block_tts_lexicon');

        // tts savepoint reached
        upgrade_block_savepoint(true, 2012101800, 'tts');
    }
    
    if ($oldversion < 2012102900)
    {
        // Correction on the wrong table name
        upgrade_block_savepoint(true, 2012102900, 'tts');
    }
    if ($oldversion < 2012110500)
    {
        // Fixed language string issue
        upgrade_block_savepoint(true, 2012110500, 'tts');
    }
    if ($oldversion < 2012121000)
    {
        //Update logo for Moodle 2.4
        upgrade_block_savepoint(true, 2012121000, 'tts');
    }
    if ($oldversion < 2012121400)
    {
        //Update capabilities for Moodle 2.4
        upgrade_block_savepoint(true, 2012121400, 'tts');
    }

    return true;
}

?>
