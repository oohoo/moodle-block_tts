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

class Fetch
{

    public $text;
    public $path;
    public $voice;
    public $badInit;
    public $url;
    public $audio;
    public $errorText;
    public $lexiconVersion = 0;
    public $course;

    //***override this
    public function __construct()
    {
        
    }

    //***override this: return true if MP3 recieved, otherwise false
    public function requestAudioFromService()
    {
        
    }

    //***if a service has special requirements, override this
    public function preProcessTextForService($text)
    {
        return $text;
    }

    public function getAudioNameFromText($text)
    {
        return md5(trim(strtoupper($text))) . $this->voice . '___' . $this->lexiconVersion . '.mp3';
    }

    public function getAudioURL()
    {
        return $this->url . $this->audio;
    }

    public function getAudioPath()
    {
        return $this->path . $this->audio;
    }

    //add a 0 byte clause that deletes it and returns false ***NEEDED***
    public function checkAudioExists()
    {
        if (file_exists($this->getAudioPath()))
        {
            if (filesize($this->getAudioPath()) == 0)
            {
                unlink($this->getAudioPath());
            }
        }
        return file_exists($this->getAudioPath());
    }

    //return true if path exists, otherwise false
    public function setPath()
    {
        if (!file_exists($this->path))
        {
            //Linux file permissions.  See known issues.
            mkdir($this->path, 0775, true);
        }
        if (!file_exists($this->path))
        {
            throw new Exception("Folder: " . $this->path . " cannot be written to, or does not exist.  Please write parent directories also or adjust permissions so that php can write");
            return true;
        }
        return false;
    }

    public function setText($text)
    {
        $this->text = $this->preProcessTextForService($text);
    }

}

?>