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
/*
 * 
 * The original code was for MP3's only. In the microsoft service we are recieving wavs; we therefore are overwriting
 * many function to work with wav's, despite being called functionMP3doSomething();
 *  
 */
require_once('service_lib.php');

class MicrosoftFetch extends Fetch
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

    public function getMP3NameFromText($text)
    {
        return md5(trim(strtoupper($text))) . $this->voice . '___' . $this->lexiconVersion . '.wav';
    }

    public function preProcessTextForService($text)
    {
        $text = trim($text);
        $text = html_entity_decode($text);
        $text = Lexicon($text, $this->lexiconVersion);

        return $text;
        //return preg_replace("/[^a-zA-Z0-9\s]/" , "" , $text);
    }

    public function requestMP3FromService()
    {
        //check if text is empty, if it is, request something else instead

        $appID = 'D96C8261DFDD2B208A720736D6D49473E081A000';

        //$text = html_entity_decode($this->text);
        $text = urlencode($this->text);
        //print $this->text;
        //$text = rawurlencode(html_entity_decode(($this->text)));



        $lang = $this->voice;
        $format = 'audio%2fwav';
        $ajaxURL = 'http://api.microsofttranslator.com/V2/http.svc/Speak?appId=' . $appID . '&text=' . $text . '&language=' . $lang . '&format=' . $format;



        if (empty($this->text))
        {
            $this->setText($this->errorText);
            $this->mp3 = $this->getMP3NameFromText($this->text);
        }

        if (file_put_contents($this->getMP3Path(), file_get_contents($ajaxURL)) == true)
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
                    //do check on banned words and attempt a preg_replace on the transcription text
                    //should replace this with a word replacement array and iterate over it.
                    //pronunciation can be improved here.
                    //BUILD A LEXICON/TRAINER
                    //$text = preg_replace("/Resume/" , "resumay" , $text);
                    if (file_put_contents($this->getMP3Path(), file_get_contents($ajaxURL)) == true)
                    {
                        return true;
                    }
                }
            }
        }
    }

}

//http://api.microsofttranslator.com/V2/http.svc/Speak?appId=TaSST0u3dBrNrL7YXMlJ-ht2Olhpq9-m1JHgOgxIB2u3fXRZbH8GK_UZZBBDfJVVV&text=test&language=en&format=audio%2fwav 
?>