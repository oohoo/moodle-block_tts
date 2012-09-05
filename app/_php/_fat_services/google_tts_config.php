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
class GoogleFetch extends Fetch {
		
	public function __construct($path , $url, $voice=null) {
		parent::__construct();
		
		//voice was checked in fetcher.  this class is for saving on code, not avoiding coupling.
		$this->path = $path;
		$this->url = $url;
		$this->badInit = !file_exists($this->path);
	}
	
	public function preProcessTextForService($text) {
		$text = trim($text);
                $text = html_entity_decode($text);
                $text = Lexicon($text, $this->lexiconVersion);
		return $text;
                //return preg_replace("/[^a-zA-Z0-9\s]/" , "" , $text);
	}
	
}
?>