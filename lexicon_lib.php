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

$operation = optional_param('oper', 'get', PARAM_TEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);

if ($courseid == 0)
{
    print 0;
    exit();
}

require_login($courseid);

$context = get_context_instance(CONTEXT_COURSE, $courseid);

if (!has_capability('block/tts:lexicon', $context))
{
    print 0;
    exit();
}

switch ($operation)
{
    case 'get':
        get_table_records($courseid);
        break;

    case 'edit':
        edit_record();
        break;

    case 'del':
        del_record();
        break;

    case 'add':
        add_record($courseid);
        break;

    default:break;
}

/**
 * Add a record to the lexicon table
 * @global moodle_database $DB
 * @param int $courseid The course ID to the lexicon
 * @return null Return nothing. The result is printed
 */
function add_record($courseid)
{
    global $DB;
    $expression = optional_param('expression', '', PARAM_TEXT);
    $prenounce = optional_param('prenounce', '', PARAM_TEXT);

    if ($expression == '' || $prenounce == '')
    {
        print 0;
        return;
    }

    //Don't want same expression with multiple meanings
    $sql = 'SELECT * FROM {block_tts_lexicon} t WHERE t.expression = \'' . $expression . '\'';
    if ($DB->count_records_sql($sql))
    {
        print 0;
        return;
    }


    $record = new stdClass();
    $record->expression = $expression;
    $record->prenounce = $prenounce;
    $record->courseid = $courseid;
    $record->lastmodified = time();

    if ($id = $DB->insert_record('block_tts_lexicon', $record, true))
    {
        print $id;
    }
    else
    {
        print 0;
    }
}

/**
 * Delete a record from the lexicon table
 * @global moodle_database $DB
 * @return null Return nothing. The result is printed 
 */
function del_record()
{
    global $DB;
    $id = optional_param('id', 0, PARAM_INT);

    if ($id == 0)
    {
        print 0;
        return;
    }

    if ($DB->delete_records('block_tts_lexicon', array('id' => $id)))
    {
        print 1;
    }
    else
    {
        print 0;
    }
}

/**
 * Edit a record in the lexicon table
 * @global moodle_database $DB
 * @return null Return nothing. The result is printed 
 */
function edit_record()
{
    global $DB;
    $id = optional_param('id', 0, PARAM_INT);
    $expression = optional_param('expression', '', PARAM_TEXT);
    $prenounce = optional_param('prenounce', '', PARAM_TEXT);

    if ($id == 0)
    {
        print 0;
        return;
    }

    $record = new stdClass();
    $record->id = $id;
    $record->expression = $expression;
    $record->prenounce = $prenounce;
    $record->lastmodified = time();

    if ($DB->update_record('block_tts_lexicon', $record))
    {
        print 1;
    }
    else
    {
        print 0;
    }
}

/**
 * Return le list of lexicon records for a course ID. The return is directly printed in json
 * @global moodle_database $DB
 * @param int $courseid The course ID
 */
function get_table_records($courseid)
{
    global $DB;

    $page = optional_param('page', 1, PARAM_INT);
    $rows = optional_param('rows', 50, PARAM_INT);
    $sidx = optional_param('sidx', 'expression', PARAM_TEXT);
    $sord = optional_param('sord', 'DESC', PARAM_TEXT);

    $count = (int) $DB->count_records('block_tts_lexicon', array('courseid' => $courseid)); //mysql_query("SELECT COUNT(*) AS count FROM invheader"); 
    // calculate the total pages for the query 
    if ($count > 0 && $rows > 0)
    {
        $total_pages = ceil($count / $rows);
    }
    else
    {
        $total_pages = 0;
    }

    // if for some reasons the requested page is greater than the total 
    // set the requested page to total page 
    if ($page > $total_pages)
        $page = $total_pages;

    // calculate the starting position of the rows 
    $start = $rows * $page - $rows;

    // if for some reasons start position is negative set it to 0 
    // typical case is that the user type 0 for the requested page 
    if ($start < 0)
        $start = 0;

    // the actual query for the grid data 
    //$SQL = "SELECT invid, invdate, amount, tax,total, note FROM invheader ORDER BY $sidx $sord LIMIT $start , $limit"; 
    $results = $DB->get_records('block_tts_lexicon', array(), "$sidx $sord", '*');

    $table_rows = array();

    foreach ($results as $row)
    {
        $table_rows[] = array('id' => $row->id, 'expression' => $row->expression, 'prenounce' => $row->prenounce);
    }

    $table = array('page' => $page, 'total' => $total_pages, 'records' => $count, 'rows' => $table_rows);

    print json_encode($table);
}

?>
