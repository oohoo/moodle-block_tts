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
 */
require_once('service_lib.php');
class Fetch {
	
    public $lexiconVersion = 0;
    
	//file system location
	public $path;
	
	//www location
	public $url;
        
        //The voice
	public $voice;
	
	//***override this
	public function __construct() {}
	
	//***if a service has special requirements, override this (Example: does not take numbers)
	//public function preProcessTextForService($text) { return $text; }			
	
	//add a 0 byte clause that deletes it and returns false ***NEEDED***
	public function checkMP3Exists($text) {
		
		$text = $this->preProcessTextForService($text);
		$mp3_file = md5( trim( strtoupper($text) ) ) . $this->voice . '___' . $this->lexiconVersion . '.mp3' ;
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