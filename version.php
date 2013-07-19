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

$plugin->version  = 2013071900;
$plugin->requires = 2011120500; 
$plugin->maturity = MATURITY_BETA;
$plugin->release = '1.0.5 (Build: 2013071900)';
$plugin->component = 'block_tts';
$plugin->cron = 3600; /// Set min time between cron executions to 300 secs (5 mins)
