<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    Language Lab TTS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/*
 *  Original TTS Code By: Ryan Thomas in association with the Neil Squire Society
 *  Integrated into Moodle 2.0 and Modified by Dustin Durand
 * 
 * The original code was for MP3's only. In the microsoft service we are recieving wavs; we therefore are overwriting
 * many function to work with wav's, despite being called functionMP3doSomething();
 *  
 */
require_once('service_lib.php');
class MicrosoftFetch extends Fetch {
		
	public function __construct($path , $url, $voice) {
		parent::__construct();
		
		//voice was checked in fetcher.  this class is for saving on code, not avoiding coupling.
		$this->path = $path;
		$this->url = $url;
		$this->badInit = !file_exists($this->path);
                $this->voice = $voice;
	}
	
	public function preProcessTextForService($text) {
		$text = trim($text);
                $text = html_entity_decode($text);
                $text = Lexicon($text, $this->lexiconVersion);
                
		return $text;
                //return preg_replace("/[^a-zA-Z0-9\s]/" , "" , $text);
                
               
	}

        	public function checkMP3Exists($text) {
		
		$text = $this->preProcessTextForService($text);
		$mp3_file = md5( trim( strtoupper($text) ) ) . $this->voice . '___' . $this->lexiconVersion . '.wav' ;
		$mp3_path = $this->path . $mp3_file;
		
		if(file_exists($mp3_path ) ) {
			if (filesize( $mp3_path ) == 0) {
				unlink( $mp3_path );
			}
		}
		
		$result = new StdClass();
		$result->is_mp3 = file_exists($mp3_path);
		if($result->is_mp3){
			$result->mp3URL = $this->url . $mp3_file;
		}
		return $result; 
	}
        
}
?>