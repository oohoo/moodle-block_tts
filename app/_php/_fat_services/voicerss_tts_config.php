<?php

require_once('service_lib.php');
class VoiceRSSFetch extends Fetch {
		
	public function __construct($path , $url, $voice, $course) {
		parent::__construct();
		
                $this->course = $course;
		//voice was checked in fetcher.  this class is for saving on code, not avoiding coupling.
		$this->path = $path;
		$this->url = $url;
		$this->badInit = !file_exists($this->path);
                $this->voice = $voice;
                
	}
	
	public function preProcessTextForService($text) {
		$text = trim($text);
                $text = html_entity_decode($text);
                $text = Lexicon($text, $this->lexiconVersion, $this->course);
                
		return $text;          
	}

        	public function checkAudioExists($text) {
                    
		$text = $this->preProcessTextForService($text);
		$audio_file = md5( trim( strtoupper($text) ) ) . $this->voice . '___' . $this->lexiconVersion . '.wav' ;
		$audio_path = $this->path . $audio_file;
		
		if(file_exists($audio_path ) ) {
			if (filesize( $audio_path ) == 0) {
				unlink( $audio_path );
			}
		}
		
		$result = new StdClass();
		$result->is_audio = file_exists($audio_path);
		if($result->is_audio){
			$result->audioURL = $this->url . $audio_file;
		}
		return $result; 
	}
        
}
?>
