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
require_once('service_lib.php');

class GoogleFetch extends Fetch
{

    public function __construct($voice, $path, $url, $text, $errorText)
    {
        parent::__construct();

        //voice was checked in fetcher.  this class is for saving on code, not avoiding coupling.
        $this->voice = $voice;
        $this->setText($text);
        $this->path = $path;
        $this->url = $url;
        $this->errorText = $errorText;
        $this->mp3 = $this->getMP3NameFromText($this->text);
        //path should be of the form base . service . voice
        $this->badInit = $this->setPath($this->path);
    }

    public function preProcessTextForService($text)
    {
        $text = trim($text);
        $text = html_entity_decode($text);

        //do check on banned words and attempt a preg_replace on the transcription text
        //should replace this with a word replacement array and iterate over it.
        //pronunciation can be improved here.
        //BUILD A LEXICON/TRAINER
        $text = Lexicon($text, $this->lexiconVersion);
        return $text;
        //return preg_replace("/[^a-zA-Z0-9\s]/" , "" , $text);
    }

    public function requestMP3FromService()
    {


        //check if text is empty, if it is, request something else instead
        if (empty($this->text))
        {
            $this->setText($this->errorText);
            $this->mp3 = $this->getMP3NameFromText($this->text);
        }


        // &ie=UTF-8 is required for languages like French
        if (file_put_contents($this->getMP3Path(), file_get_contents("http://translate.google.com/translate_tts?tl=" . $this->voice . "&ie=UTF-8&q=" . urlencode($this->text))) == true)
        {
            return true;
        }
        else
        {
            if (file_exists($this->getMP3Path()))
            {
                if (filesize($this->getMP3Path()) == 0)
                {
                    unlink($this->getMP3Path());

                    if (file_put_contents($this->getMP3Path(), file_get_contents("http://translate.google.com/translate_tts?tl=" . $this->voice . "&ie=UTF-8&q=" . urlencode($this->text))) == true)
                    {
                        return true;
                    }
                }
            }
        }
    }

}

?>