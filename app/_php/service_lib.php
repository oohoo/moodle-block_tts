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
function Lexicon($rawText, &$lastmodified)
{
    global $DB;

    $lexicons = $DB->get_records('block_tts_lexicon', array());

    $lastMOD = 0;

    foreach ($lexicons as $lexicon)
    {

        if (preg_match('/' . $lexicon->expression . '/i', $rawText) == 0)
        {
            continue;
        }

        $rawText = preg_replace('/' . $lexicon->expression . '/i', $lexicon->prenounce, $rawText);

        if ($lexicon->lastmodified > $lastMOD)
        {
            $lastMOD = $lexicon->lastmodified;
        }
    }
    $lastmodified = $lastMOD;

    return $rawText;
}

//Converts moodle lang to microsoft speech lang abreh.
//If micrsoft doesn't support language - returns english
function lang($lang)
{

    switch ($lang)
    {

        case 'ca': return 'ca';
            break;
        case 'da': return 'da';
            break;
        case 'de': return 'de';
            break;
        case 'de_du': return 'de-de';
            break;
        case 'en': return 'en';
            break;
        case 'en': return 'en';
            break;
        case 'en_us': return 'en-us';
            break;
        case 'es': return 'es';
            break;
        case 'es_es': return 'es-es';
            break;
        case 'es_mx': return 'es-mx';
            break;
        case 'fi': return 'fi';
            break;
        case 'fr': return 'fr';
            break;
        case 'fr_ca': return 'fr-ca';
            break;
        case 'it': return 'it';
            break;
        case 'ja': return 'ja';
            break;
        case 'ko': return 'ko';
            break;
        case 'ko': return 'ko';
            break;
        case 'nl': return 'nl';
            break;
        case 'no': return 'no';
            break;
        case 'pl': return 'pl';
            break;
        case 'pt': return 'pt';
            break;
        case 'pt_br': return 'pt-br';
            break;
        case 'ru': return 'ru';
            break;
        case 'sv': return 'sv';
            break;
        case 'sv': return 'sv';
            break;
        case 'zh_cn': return 'zh-cn';
            break;
        case 'zh_tw': return 'zh-tw';
            break;

        default: return 'en';
            break;
    }
}

?>
