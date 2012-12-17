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
$string['pluginname'] = 'Text to Speech';
$string['max_requests'] = 'Max Requests';
$string['max_requestsdef'] = 'The max number ajax requests to server.';
$string['max_errors'] = 'Max Errors';
$string['max_errorsdef'] = 'Maximum amount of errors before script stops.';
$string['max_fat_fetch_attempts'] = 'Max Fat Attempts';
$string['max_fat_fetch_attemptsdef'] = 'Maximum amount of requests to the tts service (Never greater than 6).';
$string['sm_starting_volume'] = 'Default Volume';
$string['sm_starting_volumedef'] = 'Default Volume (0-100).';
$string['default_service'] = 'TTS Service';
$string['default_servicedef'] = 'The current tts service - Currently only google, or microsoft. Both Microsoft and Google have a decent amount of languages, but Google is  smoother than Microsoft (in our opinion).';
$string["lexicon"] = 'Lexicon';
$string["lexicon_help"] = 'Lexicon allow to set exception to the pronounciation...';
$string['tts:addinstance'] = 'Add a new Text to Speech block';
$string['tts:lexicon'] = 'Allow user to update the lexicon';


//GRID
$string['recordtext'] = 'View {0} - {1} of {2}';
$string['emptyrecords'] = 'No records to view';
$string['loadtext'] = 'Loading...';
$string['pgtext'] = 'Page {0} of {1}';
$string['search_caption'] = 'Search...';
$string['search_Find'] = 'Find';
$string['search_Reset'] = 'Reset';
$string['search_matchText'] = ' match';
$string['search_rulesText'] = '  rules';
$string['add_Caption'] = 'Add Record';
$string['edit_Caption'] = 'Edit Record';
$string['edit_bSubmit'] = 'Submit';
$string['edit_bCancel'] = 'Cancel';
$string['edit_bClose'] = 'Close';
$string['edit_saveData'] = 'Data has been changed! Save changes?';
$string['edit_bYes'] = 'Yes';
$string['edit_bNo'] = 'No';
$string['edit_bExit'] = 'Cancel';
$string['required'] = 'Field is required';
$string['number'] = 'Please, enter valid number';
$string['minValue'] = 'value must be greater than or equal to ';
$string['maxValue'] = 'value must be less than or equal to';
$string['email'] = 'is not a valid e-mail';
$string['integer'] = 'Please, enter valid integer value';
$string['date'] = 'Please, enter valid date value';
$string['url'] = 'is not a valid URL. Prefix required (\'http://\' or \'https://\')';
$string['nodefined'] = ' is not defined!';
$string['novalue'] = ' return value is required!';
$string['customarray'] = 'Custom function should return array!';
$string['customfcheck'] = 'Custom function should be present in case of custom checking';
$string['view_caption'] = 'View Record';
$string['view_bclose'] = 'Close';
$string['delete_caption'] = 'Delete';
$string['delete_msg'] = 'Delete selected record(s)?';
$string['delete_bSubmit'] = 'Delete';
$string['delete_bCancel'] = 'Cancel';
$string['edittext'] = '';
$string['edittitle'] = 'Edit selected row';
$string['addtext'] = '';
$string['addtitle'] = 'Add new row';
$string['deltext'] = '';
$string['deltitle'] = 'Delete selected row';
$string['searchtext'] = '';
$string['searchtitle'] = 'Find records';
$string['refreshtext'] = '';
$string['refreshtitle'] = 'Reload Grid';
$string['alertcap'] = 'Warning';
$string['alerttext'] = 'Please, select row';
$string['viewtext'] = '';
$string['viewtitle'] = 'View selected row';
$string['col_caption'] = 'Select columns';
$string['col_bSubmit'] = 'Ok';
$string['col_bCancel'] = 'Cancel';
$string['errcap'] = 'Error';
$string['nourl'] = 'No url is set';
$string['norecords'] = 'No records to process';
$string['model'] = 'Length of colNames <> colModel!';
$string['am'] = 'am';
$string['AM1'] = 'AM';
$string['pm'] = 'pm';
$string['PM1'] = 'PM';
$string["Sun"] = "Sun";
$string["Mon"] = 'Mon';
$string["Tue"] = "Tue";
$string["Wed"] = "Wed";
$string["Thr"] = "Thr";
$string["Fri"] = "Fri";
$string["Sat"] = "Sat";
$string["Sunday"] = "Sunday";
$string["Monday"] = "Monday";
$string["Tuesday"] = "Tuesday";
$string["Wednesday"] = "Wednesday";
$string["Thursday"] = "Thursday";
$string["Friday"] = "Friday";
$string["Saturday"] = "Saturday";
$string["Jan"] = "Jan";
$string["Feb"] = "Feb";
$string["Mar"] = "Mar";
$string["Apr"] = "Apr";
$string["May"] = "May";
$string["Jun"] = "Jun";
$string["Jul"] = "Jul";
$string["Aug"] = "Aug";
$string["Sep"] = "Sep";
$string["Oct"] = "Oct";
$string["Nov"] = "Nov";
$string["Dec"] = "Dec";
$string["January"] = "January";
$string["February"] = "February";
$string["March"] = "March";
$string["April"] = "April";
$string["May"] = "May";
$string["June"] = "June";
$string["July"] = "July";
$string["August"] = "August";
$string["September"] = "September";
$string["October"] = "October";
$string["November"] = "November";
$string["December"] = "December";

$string["eq"] = 'equal';
$string["neq"] = 'not equal';
$string["lt"] = 'less';
$string["le"] = 'less or equal';
$string["gt"] = 'greater';
$string["ge"] = 'greater or equal';
$string["bw"] = 'begins with';
$string["nbw"] = 'does not begin with';
$string["ii"] = 'is in';
$string["nii"] = 'is not in';
$string["ew"] = 'ends with';
$string["dew"] = 'does not end with';
$string["con"] = 'contains';
$string["dcon"] = 'does not contain';
$string["AND"] = 'all';
$string["OR"] = 'any';


$string["col_1"] = 'Expression';
$string["col_2"] = 'Pronounce';
$string["table_caption"] = 'Language Lab TTS Lexicon';
$string["err_add"] = 'Error Occured Adding.';
$string["err_del"] = 'Pronounce';
$string["err_save"] = 'Error Occured Saving.';


