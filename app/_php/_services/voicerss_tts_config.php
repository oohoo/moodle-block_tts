<?php

require_once('service_lib.php');

class VoiceRSSFetch extends Fetch
{

    public function __construct($voice, $path, $url, $text, $errorText, $course)
    {
        parent::__construct();

        //voice was checked in fetcher.  this class is for saving on code, not avoiding coupling.

        $this->course = $course;
        $this->voice = $this->lang($voice);
        $this->setText($text);
        $this->path = $path;
        $this->url = $url;
        $this->errorText = $errorText;
        $this->audio = $this->getAudioNameFromText($this->text);
        
        //path should be of the form base . service . voice
        $this->badInit = $this->setPath($this->path);
    }

    private function lang($lang) {
        
        switch($lang) {
            case "ca": return "ca-es";
            case "zh_cn": return "zh-cn";
            case "zh_tw": return "ca-tw";
            case "nl": return "nl-nl";
            case "en": return "en-ca";//en-au,en-gb,en-in,en-us also possible
            case "fi": return "fi-fi";    
            case "fr_ca": return "fr-ca";    
            case "fr": return "fr-fr";    
            case "de": return "de-de";    
            case "it": return "it-it";    
            case "ja": return "ja-jp";  
            case "ko": return "ko-kr";
            case "no": return "nb-no";
            case "pl": return "pl-pl";
            case "pt_br": return "pt-br";
            case "ru": return "ru-ru";
            case "es": return "pl-pl";//es-mx,es-es,
            case "sv": return "sv-se";
                
            default:
                return "en-ca";
        }
        
    }
    
    public function getAudioNameFromText($text)
    {
        return md5(trim(strtoupper($text))) . $this->voice . '___' . $this->lexiconVersion . '.wav';
    }

    public function preProcessTextForService($text)
    {
        $text = trim($text);
        $text = html_entity_decode($text);
        $text = Lexicon($text, $this->lexiconVersion, $this->course);

        return $text;
        //return preg_replace("/[^a-zA-Z0-9\s]/" , "" , $text);
    }

    public function requestAudioFromService()
    {
        global $CFG;
        
        $text = urlencode($this->text);

        $lang = $this->voice;
        $format = '48khz_16bit_stereo';

        $ajaxURL = 'http://api.voicerss.org?key=' . $CFG->block_tts_default_service_app_id . '&src=' . $text . '&language=' . $lang . '&f=' . $format;

        if (empty($this->text))
        {
            $this->setText($this->errorText);
            $this->audio = $this->getAudioNameFromText($this->text);
        }

        if (file_put_contents($this->getAudioPath(), file_get_contents($ajaxURL)) == true)
        {
            return true;
        }
        else
        {
            if (file_exists($this->getAudioPath()))
            {
                if (filesize($this->getAudioPath()) == 0)
                {
                    unlink($this->getAudioPath());
                    //do check on banned words and attempt a preg_replace on the transcription text
                    //should replace this with a word replacement array and iterate over it.
                    //pronunciation can be improved here.
                    //BUILD A LEXICON/TRAINER
                    //$text = preg_replace("/Resume/" , "resumay" , $text);
                    if (file_put_contents($this->getAudioPath(), file_get_contents($ajaxURL)) == true)
                    {
                        return true;
                    }
                }
            }
        }
    }

}

?>
