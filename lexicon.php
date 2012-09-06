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
$courseid = required_param('courseid', PARAM_INT);

print<<<HERE
<script type='text/javascript'>
window.courseid = $courseid;
</script>
HERE;

$context = get_context_instance(CONTEXT_COURSE, $courseid);

if (!has_capability('block/tts:lexicon', $context))
{
    redirect($CFG->wwwroot . '/course/view.php?id=' . $courseid);
}

require_login($courseid);

$PAGE->set_title('Lexicon');
$PAGE->set_url('/blocks/tts/lexicon.php');
$PAGE->navbar->add('TTS Lexicon');

$PAGE->requires->css('/blocks/tts/grid/jquery-ui-1.8.13.custom.css');
$PAGE->requires->css('/blocks/tts/grid/ui.jqgrid.css');
$PAGE->requires->css('/blocks/tts/styles.css');


$PAGE->requires->js('/blocks/tts/grid/jquery-1.5.1.min.js');
$PAGE->requires->js('/blocks/tts/grid/grid.locale.php');
$PAGE->requires->js('/blocks/tts/grid/jquery.jqGrid.min.js');
$PAGE->requires->js('/blocks/tts/grid/grid.js');

echo $OUTPUT->header();

echo '<div id="lexicon_wrapper">';
echo '<table id="lexicon_table"><tr><td></td></tr></table>';
echo '<div id="lexicon_pager"></div>';
echo '</div>';
echo $OUTPUT->footer();
?>
