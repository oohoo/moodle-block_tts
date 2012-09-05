var tts = {
	config:{
		callbacks:{},
		cache:{}, /*CACHE CODE*/
		spans:{},
		services:{},
		prefetch:{},
		phrase:{
			beforeSplitRE:/((([^\.]\s*[^A-Z]\.)|([;,\?!*]))($|(?=\s)))/g,
			afterSplitRE:/(\s(\s+\-+\s+|that|because|but|instead|and|or|which|according)(\s|$))/gi,
			libSplits:/(\s|^)(between|to|in|except|from|for|from|when|with)(\s|$)/gi,
			splitRegExp:/\_\}\_/,
			stringDelimiter:"_}_"
		},
		TTS:{}
	},
	phraseFinder:{
		splitString:function(str){
			if(typeof str !== 'string') {
				return [];
			}
			str = str.replace(tts.config.phrase.beforeSplitRE, '$1' + tts.config.phrase.stringDelimiter);
			str = str.replace(tts.config.phrase.afterSplitRE, tts.config.phrase.stringDelimiter + '$1');
			
			return tts.phraseFinder.splitByDelim(str, tts.config.phrase.stringDelimiter, true);
		},
		liberalSplit:function(str){
			str = str.replace(tts.config.phrase.libSplits, tts.config.phrase.stringDelimiter + '$1$2$3');
			return tts.phraseFinder.splitByDelim(str, tts.config.phrase.stringDelimiter, false);
		},
		trim:function(str){
			return ((str == null) ? null : str);
		},
		countWords:function(str){
			return (tts.phraseFinder.trim(str).split(/\s+/).length);
		},
		splitByCapacity:function(strToProcess){

			var segments = [];
			var whitespaces = [];
			var pattern = /\s/;
			
			for (i=0;i<strToProcess.length;i++){
				if (pattern.test(strToProcess.charAt(i))){
					whitespaces.push(i);
				}
			}
			
			if (whitespaces.length == 0){
				segments.push(strToProcess);
			}else if (whitespaces.length == 1){
				segments.push(strToProcess.slice(0, whitespaces[0] -1));
				segments.push(strToProcess.slice(whitespaces[0]));
			}else if (whitespaces.length > 1){
				for (i = 0; i < whitespaces.length; i++) {
					//check if first
					if (i==0){

						segments.push(strToProcess.slice(0, whitespaces[i+1]+1));
					}
					if (i > 0 && i != whitespaces.length -1){
					//*to debug later
						
						segments.push(strToProcess.slice(whitespaces[i] + 1, whitespaces[i+1]+1));
					}
					if (i == whitespaces.length -1){

						segments.push(strToProcess.slice(whitespaces[i] + 1));	
					}
				}
			}
			
			//For each segment. check the segment length. if it is greater than capacity, 
			for (i = 0; i < segments.length; i++){

				if (segments[i].length > tts.config.spans.CurrentSpanCapacity){
					var newSegments = [];
					while (segments[i].length > tts.config.spans.CurrentSpanCapacity){
							newSegments.push(segments[i].slice(0,tts.config.spans.CurrentSpanCapacity -1));
							segments[i] = segments[i].slice(tts.config.spans.CurrentSpanCapacity);
					}
					//for(j=0;j<newSegments.length;j++){console.log(newSegments[j]);}
					newSegments.push(segments[i]);
					newSegments.unshift(i,1);
					i+= newSegments.length + 1;
					segments.splice.apply(segments, newSegments);
					delete newSegments;
				}
			}
			delete whitespaces;
			delete pattern;
			return segments;
			
		},
		splitByDelim:function(str, delim, lib_recurse) {
			// Construct the initial array of strings.
			var phrases = str.split(tts.config.phrase.splitRegExp);
			var stillWorking = true;
			var keepWorking = false;
			// Iterate over the phrases that were found, adjusting them
			// based on number of words.    
			while (stillWorking == true){
				//keep working will become true if subsequent conditions are satisfied
				keepWorking = false;
				
				//process phrases
				for(var i=0; i<phrases.length; i++) {
				  
				  //replacement may be redundant from createspans()
				  phrase = phrases[i].replace(/\s+/g,' ');
				  //remove empty phrases to prevent infinite recursion
				  if (phrase.length == 0){
					phrases.splice(i,1);
					i--;
					continue;
				  }
				  
				  var numWords = tts.phraseFinder.countWords(phrase);

				  //add clauses to check next phrase size and deny transaction if it is overlarge
				  //possibly put both for loops in a do until to naturalize things a bit more
				  //eventually no moves will be possible and no phrases will exceede capacity
				  
				  // If it's too short, combine with one of the phrases around it.
				  //this logic produces phrases of between 3 and 7 words which are less than [capacity] characters and are broken on words from the regular expression constants
				  if(numWords<3 && phrases.length > 1) {
					if(i==0) {
					  // Prepend to next one.
					  if (phrase.length + phrases[i+1].length < tts.config.spans.CurrentSpanCapacity){
						//keepWorking = true;
						phrases[i+1] = phrase + phrases[i+1];
						phrases.splice(i,1);
						i--;
					  }
					} else if(i==phrases.length-1) {
					  // Append to last one.
						if (phrase.length + phrases[i-1].length < tts.config.spans.CurrentSpanCapacity){
							//keepWorking = true;
							phrases[i] = phrases[i-1] + phrase;
							phrases.splice(i-1,1);
							i--;
						}
					} else {
						// Combine with the smaller of its neighbors.
						if(phrases[i-1].length < phrases[i+1].length) {
							if (phrase.length + phrases[i-1].length < tts.config.spans.CurrentSpanCapacity){
								//keepWorking = true;
								phrases[i] = phrases[i-1] + phrase;
								phrases.splice(i-1,1);
								i--;
							}
						} else {
							if (phrase.length + phrases[i+1].length < tts.config.spans.CurrentSpanCapacity){
								//keepWorking = true;
								phrases[i+1] = phrase + phrases[i+1];
								phrases.splice(i,1);
								i--;
							}
						}
					}
				  } else if(numWords > 6 && lib_recurse) {  // Break long strings liberally.
					//keepWorking = true;
					var newPhrases = tts.phraseFinder.liberalSplit(phrase);
					newPhrases.unshift(i, 1);
					i+= newPhrases.length + 1;
					phrases.splice.apply(phrases, newPhrases);
					delete newPhrases;
				  }
				}
				
				for(var i=0; i<phrases.length; i++) {
					if (phrases[i].length > tts.config.spans.CurrentSpanCapacity){
						keepWorking = true;
						var newPhrases = tts.phraseFinder.splitByCapacity(phrases[i]);
						newPhrases.unshift(i, 1);
						i+= newPhrases.length + 1;
						phrases.splice.apply(phrases, newPhrases);
						delete newPhrases;
					}
				}
				
				if (keepWorking == true){
					//console.log('keep working');
					stillWorking = true;
				}
				else{
					stillWorking = false;
				}
			}
			delete stillWorking;
			delete keepWorking;
			delete numWords;
			return phrases;
		}			
	},
	init:{
		getJSONConfig:function(){
			//make a post request for a JSON object.
			$.ajax({
				asynch:false,
				cache:false,
				dataType:'json',
				error:function(){},
				global:false,
				success:function(result){
					var obj = result;
					if (typeof obj !== 'undefined'){
						var course = tts.config.TTS.course;
						$.extend(tts.config,obj);
						$.extend(tts.config.TTS,{TTSConfigured:true,course:course});
						delete course;
						delete tts.config.TTS.TTSConfigurationRequested;
						/*BROWSER HACK vs IE 'FEATURE'*/
						
						if(typeof tts.config.cache.ie === 'undefined'){
							$.extend(tts.config.cache,{ie:$.browser.msie});
							if(tts.config.cache.ie){
								var timestamp = new Date().getTime();
								//tts.config.TTS.SM_SWF_URL = tts.config.TTS.SM_SWF_URL + '?' + String(timestamp);
								//console.log(tts.config.TTS.SM_SWF_URL);
								tts.init.startSoundManager2();							
							}
							else{
								//console.log('not ie');
								tts.init.startSoundManager2();
							}
						
						}
						else{
						tts.init.startSoundManager2();
						}
						/*---*/
						
						/*CACHE CODE - check if the browser can do this*/
						$.storage = new $.store();
						
						if($.storage.driver.scope == "browser"){
							$.extend(tts.config.cache,{enabled:true});
						}
						else{
							$.extend(tts.config.cache,{enabled:false});
						}	
					}
					else{
						setTimeout(tts.init.getJSONConfig,500);
					}
				},
				timeout:15000,
				type:"GET",
				url:tts.config.TTS.TTSConfigURL
			});
		},
		startSoundManager2:function(){
			//lazy loading sound manager 2 to accomadate non-automatic starting of TTS
			window.SM2_DEFER = true;
			var smScript = document.createElement('script');
			smScript.type = 'text/javascript';
			smScript.onreadystatechange = function(){
				var rs = this.readyState;
				if (rs == 'loaded' || rs == 'complete') {
					this.onreadystatechange = null;
					this.onload = null;
					tts.init.smScriptLoaded();
				}
			};
			smScript.onload = function(){
				this.onreadystatechange = null;
				this.onload = null;
				window.setTimeout(function(){tts.init.smScriptLoaded()},20);			
			};
			smScript.src = tts.config.TTS.SM_SCRIPT_URL;
			document.getElementsByTagName('head')[0].appendChild(smScript);
		},
		smScriptLoaded:function(){
			//console.log('sm2 script loaded');
			soundManager = new SoundManager();
			//would be better to do this in a key value loop and provide them through config.php
			soundManager.onready(function(){
				//console.log('ready');
				//do the successful initialization callback which may affect the UI.
				if (typeof tts.config.callbacks.initSuccess == 'function'){
					tts.config.callbacks.initSuccess();
				}
			});
			//if SM2 times out, do the fail callback so UI can be informed.
			soundManager.ontimeout(function(){
				//console.log('ontimeout');
				if (typeof tts.config.callbacks.initFail == 'function'){
					tts.config.callbacks.initFail();
				}
			});			
			$.extend(soundManager,{
				url:tts.config.TTS.SM_SWF_URL,
				useConsole:true,
				debugMode:false,
				consoleOnly:false,
				waitForWindowLoad:true,
				defaultOptions:{
					glboalMute:false,
					premute:tts.config.TTS.SM_STARTING_VOLUME,
					multiShot:false,
					autoLoad:true,
					volume:tts.config.TTS.SM_STARTING_VOLUME				
				}
			});
			soundManager.beginDelayedInit();
			//console.log(soundManager);
		},
//also rebuilds spans... this should be renamed.
		wipeSpanData:function(){
			var timer = 0;
			if (typeof tts.config.spans.spanCollection !== 'undefined'){
				tts.config.spans.spanCollection
					.each(function(){
						$this = $(this);
						if (typeof $this.data('sound') !== 'undefined'){
							//SoundManager2 instance is destroyed
							$this.data('sound').destruct();
							$this.removeData('sound');
						}
						$this
							.removeData('errors')
							.removeData('state')
							.removeData('file')
							.removeData('number')
							.removeData('dirty_mp3')
							.removeData('error_sound_loaded')
							.removeData('text_to_speak')
							.removeData('bad_mp3_url')
							.unbind();
						//if cache is enabled, destroy that data too
						if (typeof tts.config.cache.enabled !== 'undefined' && tts.config.cache.enabled === true ){
							$this
								.removeData('key')
								.removeData('value')
								.removeData('stored');
						}
					});
				/*CACHE CODE - wipe out cache fields here.  Also reset global caching variables*/
				//global state variables pertaining to caching should also be reset.
				//reseting the timestamp so that Internet Explorer is happy with the new mp3s is necessary
				//This if clause executes ONLY IF this is NOT the first time spans are being wiped of data
				//This is prudent if a voice is being changed without refreshing the page.
				if(tts.config.spans.spanCollection.length > 0){
					//recursing until execution of function is appropriate
					function delayedBuildSound(f,param,time){
						setTimeout(function(){f(param);},time);
					}
					//reinitialize dirty IE work arounds
					var timer = 100;
					var timestamp = new Date().getTime();
					var dirty = String(timestamp) + '?' + String(timestamp);
					tts.config.spans.spanCollection.each(function(n){
						$this = $(this);
						$this
							.data('errors',0)
							.data('state',tts.config.prefetch.NOT_PREFETCHED)
							.data('number',n)
							.data('dirty_mp3',false)
							.data('error_sound_loaded',false)
							.data('bad_mp3_url','empty')
							.data('text_to_speak',$this.text().replace(/\s+/g,' '))
							.click( function( event ) {
								$this = $(this);
								if ($this.parents("a").length > 0){
										tts.spanController.pauseAll();
										return;
								}
								if(n == tts.config.spans.currentSpan){
									
									if(typeof $this.data('sound') !== 'undefined'){
										//check playstate and readystate
										if ($this.data('sound').readyState == 3){
											if($this.data('sound').playState == 1){
												tts.spanController.pauseAll();
											}
											else{
												tts.spanController.pauseAll();
												tts.spanController.playCurrentSpan();
											}
										}
									}
								}else{
									tts.spanController.skip(n - tts.config.spans.currentSpan);
								}
							});
						/*
						CACHE CODE - if there is caching in the browser(global), generate .data('key')
						Also use data('key') to set data('stored') by looking up if the key value exists.
						This will be used by other functions to prevent unnecessary server load.
						*/							
						if (typeof tts.config.cache.enabled !== 'undefined' && tts.config.cache.enabled === true ){
							//console.log('can cache');
							$this.data('key',hex_md4(tts.config.services.currentService + tts.config.services.currentVoice + $this.data('text_to_speak')));
							//check if stored.
							if ($.storage.get($this.data('key')) !== null){
								$this
									.data('stored',true)
									.data('value',$.storage.get($this.data('key')))
									.data('state',3);	
							
							if(tts.config.cache.ie){
								$this.data('file',$this.data('value') + '?' +dirty );				
							}
							else{
								$this.data('file',$this.data('value'));
							}
							//debug code.  uncomment to get more information about what is happening with caching.		
								//console.log($this.data('key'));
								//console.log($this.data('stored'));
								//console.log($this.data('value'));
								//console.log($this.data('file'));
								//console.log($this.data('state'));
								//$("#xhtml").append('<p>building sound from cache '+$this.data('file')+'</p>');
								//$("#xhtml").append($this);
								//$("#xhtml").append(this);
								delayedBuildSound(tts.prefetcher.buildSound,$this,timer);
								//setTimeout(function(){tts.prefetcher.buildSound($this);},timer); //fix for ie threading.
								//tts.prefetcher.buildSound($this);
								timer = timer + 200;
							}
							else{
								$this.data('stored',false);
							}
						}

					});
					
					/*BROWSER HACK - ie*/
					
					if (tts.config.cache.ie){
						var timestamp = new Date().getTime();
						var dirtyTime = String(timestamp) + '?' + String(timestamp);
						$.extend(tts.config.cache,{ieDirty:dirtyTime});
					}
					
					
					/*--- */
					$.extend(tts.config.spans,{
						currentSpan:0,
						previousSpan:-1,
						nextSpanToPlay:0,
						firstPlay:true,
						playingSpans:0,
						notWaitingToPlayCurrent:true,
						globalMute:false
					});
					$.extend(tts.config.prefetch,{
						blockFatFetch:false,
						fatFetchInProgress:false,
						fatFetchAttempts:0,
						firstErrorPass:true,
						unfinishedSounds:[],
						errorSpans:[],
						alreadyReporting:false,
						blockDialogFetch:true
					});
					/*CACHE CODE - reset globals here*/
				}
				if(typeof tts.config.prefetch.MAX_REQUESTS !== 'undefined'){
					//start logging requests
					if(typeof tts.config.prefetch.currentRequests !== 'undefined'){
						delete tts.config.prefetch.currentRequests;
					}					
				}
				if (typeof tts.config.SM !== 'undefined' && typeof tts.config.SM.HAS_ONREADY !== 'undefined'){
					delete tts.config.SM;
				}
			}
		},
		createSpans:function(){
			if (typeof tts.config.spans.spanCollection !== 'undefined'){
				tts.config.spans.spanCollection
					.each(function(){
						$(this).replaceWith($(this).contents());
					});
			}
			//creates spans by capacity and phrase rules currentSpan
			
                        //var test = $(tts.config.spans.selector).contents();
                        
                        var ret = [];
			//#contents in this case
			$(tts.config.spans.selector)
				.contents()
				//everything except the configuration blacklist
				.not(tts.config.spans.not)
				.each(function(){
					//if the node is a text node
					if (this.nodeType == 3)
						ret.push(this);
					else
						//drill deeper.  this node is an ancestor of a text node.
						//I think arguments.callee is depreciated in ECMA 5.
						$(this).contents().not(tts.config.spans.not).each(arguments.callee);
				});
			//for each text node in the array
			$(ret).each(function(i){
					$this = $(this);
					var temp_str ='';
					//debugging
					//$("#xhtml").append('text node: '+$this.text()+' ');
					
					//the phrases in the text node are returned by phrase finder
					var phrases = tts.phraseFinder.splitString($this.text());
					//each text node becomes an array of phrases, each phrase is assigned a number and wrapped in a <span>
					for(var j=0; j<phrases.length; j++) {
					//$("#xhtml").append(' phrase['+ j +']: '+phrases[j]);
						if(/\w/.test(phrases[j])) {
							//normalize spaces across browsers.  This allows consistent hashing and caching.
							phrases[j] = phrases[j].replace(/\s+/g,' ');
							phrases[j] = '<span class="'+ tts.config.spans.spanClass +'">' + phrases[j] + '</span>'
							temp_str = temp_str + phrases[j];
						}
					}
					if(/\w/.test(temp_str)) {
						$this.replaceWith(temp_str);
					}
					
					delete temp_str;
					delete phrases;
				});
			$.extend(tts.config.spans,{spanCollection:$("span." + tts.config.spans.spanClass)});
			
		},
		//TTS starts here.
		initialize:function(obj,success,fail){	
			//setup url, service, voice if supplied then destory obj
			if ( typeof obj === 'object') {
				if (obj.url){
					if( typeof obj.url === 'string'){
					$.extend(tts.config.TTS, {TTSConfigURL:obj.url});
					}
				}
				//google, yahoo, festival, ATT, etc.
				if (obj.service){
						if( typeof obj.service === 'string'){
							$.extend(tts.config.services, {requestedService:obj.service});
						}
				}
				//voice that service is using
				if (obj.voice){
						if( typeof obj.voice === 'string'){
							$.extend(tts.config.services, {requestedVoice:obj.voice});
						}					
				}
				
				if (obj.course){
						if( typeof obj.course === 'number'){
							$.extend(tts.config.TTS, {course:obj.course});
						}
				}
				
				$.each( obj || {}, function( key, value ) {
					delete obj[key];
				});
				if(success){
					if (typeof success === 'function'){
						//console.log('success');
						$.extend(tts.config.callbacks, {initSuccess:success});
					}
				}
				if(fail){
					if (typeof fail === 'function'){
						//console.log('fail');
						$.extend(tts.config.callbacks, {initFail:fail});
					}
				}
			}
			
			//if tts is configured, check if service is different than current service
			//IF TTS is NOT YET set up, the else clause is executed.
			if (typeof tts.config.TTS.TTSConfigured !=='undefined'){
				//see if there is a service change request and if it is to the same as the current service
				if ((typeof tts.config.services.requestedService !== 'undefined')&&(tts.config.services.requestedService != tts.config.services.currentService)&&(typeof tts.config.services.requestedService === 'string' && $.inArray( tts.config.services.requestedService, tts.config.services.serviceList ) != -1 )){
					$.extend(tts.config.services, {currentService:tts.config.services.requestedService,newService:true});
					delete tts.config.services.requestedService;
				}
				//see if there is a voice change request
				if ((typeof tts.config.services.requestedVoice !=='undefined')&&(tts.config.services.requestedVoice != tts.config.services.currentVoice)){
					//see if the voice change request is in the current voices for the service
					if (typeof tts.config.services.requestedVoice == 'string' && $.inArray( tts.config.services.requestedVoice, tts.config.services[tts.config.services.currentService].voices ) != -1 ) {
						$.extend(tts.config.services, {currentVoice:tts.config.services.requestedVoice,newVoice:true});
						delete tts.config.services.requestedVoice;
					}
					else{
						//voice is not in voice list for service, check if there is a new service being used so that a default can be set for it
						if(typeof tts.config.services.newService !== 'undefined'){ //new service, bad voice
							$.extend(tts.config.services, {currentVoice:ts.config.services[tts.config.services.currentService].defaultVoice,newVoice:true});
							delete tts.config.services.requestedVoice;
						}		
					}				
				}
				//if the service is changed, check to see that span pharases need to have different capacities
				if((typeof tts.config.services.newService !== 'undefined') && (tts.config.spans.CurrentSpanCapacity != tts.config.services[tts.config.services.currentService].capacity)){					
					delete tts.config.services.newService;
					$.extend(tts.config.spans,{CurrentSpanCapacity:tts.config.services[tts.config.services.currentService].capacity});
					$.extend(tts.config.prefetch,{startFetching:true});
					tts.init.createSpans();
					
				}
				
				//if the voices have changed (always the case if service has changed), redo the span data. and restart the prefetcher.
				if(typeof tts.config.services.newVoice !== 'undefined'){
					delete tts.config.services.newVoice;
					$.extend(tts.config.prefetch,{startFetching:true});
					tts.init.wipeSpanData();
				}
				//start Prefetcher if things have changed.  It will destroy its submission then rebuild it and call itself.
				if(typeof tts.config.prefetch.startFetching !== 'undefined'){
					delete tts.config.prefetch.startFetching;
					//**************may need to do the SM2 enabled check here.
					delete tts.config.prefetch.currentRequests;
					
					//tts.prefetcher.fetch(-1);
					tts.prefetcher.fetchController();
					/*CACHE CODE - call prefetch director here instead.*/
				}
			}
			else{ //tts is not configured, call the configuration script
				if (typeof tts.config.TTS.TTSConfigURL === 'string'){
					//if it has already requested config.php, it waits and checks 1000ms later
					if (typeof tts.config.TTS.TTSConfigurationRequested !== 'undefined'){
						setTimeout(tts.init.initialize,1000);
					}
					else{
					//config.php request is made and the fact that the request has been made is registered.
						tts.init.getJSONConfig();
						$.extend(tts.config.TTS,{TTSConfigurationRequested:true});
						setTimeout(tts.init.initialize,2500);
					}
				}
				else{
					alert('invalid config URL');
				}
			}
		}
	},
	prefetcher:{
		//attempts fatfetch of all mp3s first then begins fetch which dialogs with the server to build the mp3 library server-side.
		//fetchController has many state variables which control application flow.
		fetchController: function(){
			//console.log('fetchController called');
			if(typeof soundManager === 'undefined'){
				setTimeout(tts.prefetcher.fetchController,500);
				//console.log('no sm2');
				return;
			}
			if(!soundManager.enabled){
				setTimeout(tts.prefetcher.fetchController,500);
				//console.log(soundManager.enabled);
				return;			
			}
			
			if (tts.config.prefetch.blockFatFetch === false && typeof tts.config.prefetch.blockFatFetch !== 'undefined' && tts.config.prefetch.fatFetchInProgress === false){
				//console.log('inside fetch controller');
				tts.config.prefetch.fatFetchInProgress = true;
				var allSpans = tts.config.spans.spanCollection.length;
				var datas = {
					"span_count":allSpans,
					"voice":tts.config.services.currentVoice,
					"service":tts.config.services.currentService,
					"course":tts.config.TTS.course
				};
				
				//collect all of the spans with status of 0.
				var datas_has_data = false;
				var obj={};
				//console.log('allspans '+allSpans);
				for (i=0;i<allSpans;i++){
					var $span = tts.config.spans.spanCollection.eq(i);
					//console.log($span.data('state'));
					if ($span.data('state') == tts.config.prefetch.NOT_PREFETCHED){
						datas_has_data = true;
						var $property_span ="span_"+i;
						var $property_text ="text_"+i;
						obj[$property_span] = i;
						obj[$property_text] = $span.data('text_to_speak');					
					}					
				}
				//console.log(obj);
				$.extend(datas,	obj);
				//console.log(datas);
				//console.log(obj);
				
				if (datas_has_data !== false){
					//console.log('datas has data attemps '+tts.config.prefetch.fatFetchAttempts);
					tts.config.prefetch.fatFetchAttempts += 1;
					$.ajax({
						asynch:false,
						cache:false,
						context:$span,
						data:datas,global:false,
						dataType:'json',
						beforeSend: function(x) {
							if(x && x.overrideMimeType) {
								x.overrideMimeType("application/j-son;charset=UTF-8");
							}
						},
						timeout:60000,
						type:"POST",
						url:tts.config.prefetch.FAT_FETCH_URL,
						error:function(){
							if (tts.config.prefetch.fatFetchAttempts <= tts.config.prefetch.maxFatFetchAttempts){
								tts.config.prefetch.fatFetchInProgress = false;
								tts.prefetcher.fetchController();
							}
							else{
								//do not do this again
								tts.config.prefetch.blockFatFetch = true;
								tts.config.prefetch.blockDialogFetch = false;
								tts.config.prefetch.fatFetchInProgress = false;
								tts.prefetcher.fetchController();
							}
						},
						success:function(result){
							//console.log(result);
//							$("#xhtml").append('<p>performed fat fetch</p>');
							if (typeof result === 'object'){
								$.each(result, function(j,val) {
									$.each(val, function(k,val2){
										var keySpan = tts.config.spans.spanCollection.eq(val2.span);
										//console.log(val2);
										//console.log(val2.span);
										//console.log(val2.url);
										if (!keySpan.data('sound')){
											keySpan.data('state',tts.config.prefetch.SERVER_HAS_MP3);
											keySpan.data('file',val2.url.replace("\\",""));
											tts.prefetcher.buildSound(keySpan);
											//console.log(keySpan.data('state'));
											//console.log(keySpan.data('file'));
											//console.log(keySpan.data('number'));
											//console.log(keySpan);
										}								
									});

								});
							}
							tts.config.prefetch.blockFatFetch = true;
							tts.config.prefetch.blockDialogFetch = false;
							tts.config.prefetch.fatFetchInProgress = false;
							tts.prefetcher.fetchController();	
							
						}
					});//ajax end

				}
				
			}
			
			if(tts.config.prefetch.blockDialogFetch === false && typeof tts.config.prefetch.blockDialogFetch !== 'undefined'){
				tts.prefetcher.fetch(-1);
			}
			
		},
		//n is the number attached to a span in the SpanCollection.  $.data('number')
		fetch: function(n){
			//if there is no soundManager2, try 1 second from now.
			if (typeof soundManager.enabled == 'undefined' || !soundManager.enabled){
				setTimeout(function(){tts.prefetcher.fetch(n)},1000);
				return;
			}
			
			//if no specific span is specified, check that ALL spans have a sound attached.  If they do, terminate.
			if (n == -1){//no index is being referenced.
				
				if(typeof tts.config.prefetch.MAX_REQUESTS !== 'undefined'){
					//start logging requests
					if(typeof tts.config.prefetch.currentRequests === 'undefined'){
						//create the variable
						//current requests is HOW MANY fetchers should be allowed to run simultaneously.
						//this impacts performance but should be increased when building cache from scratch.
						$.extend(tts.config.prefetch,{currentRequests:1});
					}
					//start making more fetchers
											
				}
				
				var fetchIndex;
				//if a span needs a sound, attach a 'fetcher' process to it.
				for (i=tts.config.spans.currentSpan;i<tts.config.spans.spanCollection.length;i++){
					if (tts.config.spans.spanCollection.eq(i).data('state') == tts.config.prefetch.NOT_PREFETCHED){
						//tts.config.spans.spanCollection.eq(i).data('state',tts.config.prefetch.PREFETCH_IN_PROGRESS);
						fetchIndex = i;
						break;
						//tts.prefetcher.fetch(i);
						//return;
					}
				}
				
				if (typeof fetchIndex === 'undefined'){
					for (i=0;i<tts.config.spans.currentSpan;i++){
						if (tts.config.spans.spanCollection.eq(i).data('state') == tts.config.prefetch.NOT_PREFETCHED){
							//tts.config.spans.spanCollection.eq(i).data('state',tts.config.prefetch.PREFETCH_IN_PROGRESS);
							fetchIndex = i;
							break;
							//tts.prefetcher.fetch(i);
							//return;
						}
					}
				}
				
				if (typeof fetchIndex !== 'undefined'){
					tts.config.spans.spanCollection.eq(fetchIndex).data('state',tts.config.prefetch.PREFETCH_IN_PROGRESS);
					tts.prefetcher.fetch(fetchIndex)
				}
				
				delete fetchIndex;
				
				//if more workers should be called, spawn a new fetcher process
				if(tts.config.prefetch.currentRequests <= tts.config.prefetch.MAX_REQUESTS){
					//start another fetcher which will do the same
					tts.config.prefetch.currentRequests = tts.config.prefetch.currentRequests + 1;
					setTimeout(function(){tts.prefetcher.fetch(-1)},1000);
				}
			}
			else{
				//ask server for mp3 that is not yet server-side cached
				$span = tts.config.spans.spanCollection.eq(n);
				if ($span.data('state') < tts.config.prefetch.SERVER_HAS_MP3){
					//get rid of anything that is not alphanumeric
					var textToSpeak = $span.text();
					textToSpeak = textToSpeak.replace(/\s+/g,' ');
					//JSON request
					var data =
						{
							"service":tts.config.services.currentService,
							"voice":tts.config.services.currentVoice,
							"span": n,
							"text":	$span.data('text_to_speak'),
							"state": $span.data('state'),
							"course":tts.config.TTS.course
						};
					$.ajax({
						asynch:false,
						cache:false,
						context:$span,
						//data:dataString,
						data:data,
						dataType:'json',
						beforeSend: function(x) {
							if(x && x.overrideMimeType) {
								x.overrideMimeType("application/j-son;charset=UTF-8");
							}
						},
						error:function(){
							//increment errors if an error is thrown.  change span state to SPAN_THREW_ERROR if MAX ERRORS are reached.
							if (typeof this.data('errors') !== 'undefined'){
								$this.data('errors',$this.data('errors') + 1);
								if (this.data('errors') >= tts.config.prefetch.MAX_ERRORS){
									this.data('state',tts.config.prefetch.SPAN_THREW_ERROR);
									setTimeout(function(){tts.prefetcher.fetch(-1)},2000);
									return;
								}
							}
							setTimeout(function(){tts.prefetcher.fetch($this.data('number'))},2000);
						},
						global:false,
						success:function(result){
							var obj = result;
							obj.file = obj.file.replace("\\","");

							//call again
							if (obj.state < tts.config.prefetch.SERVER_HAS_MP3){
								this.data('state',obj.state);
								
								setTimeout(function(){tts.prefetcher.fetch(obj.span)},2500);
							}
							//build sound routine
							if (obj.state == tts.config.prefetch.SERVER_HAS_MP3){
								this.data('state',obj.state);

								if (!this.data('sound')){
									this.data('file',obj.file);
									this.data('state',obj.state);
									tts.prefetcher.buildSound(this);
								}
								setTimeout(function(){tts.prefetcher.fetch(-1)},500);
							}
							if (obj.error){	

								if (typeof this.data('errors') !== 'undefined'){
									this.data('errors', this.data('errors') + 1);
									if (this.data('errors') > tts.config.prefetch.MAX_ERRORS){
										this.data('state',tts.config.prefetch.SPAN_THREW_ERROR);

										setTimeout(function(){tts.prefetcher.fetch(-1)},2500);
										return;
									}
								}
								var num = this.data('number');
								setTimeout(function(){tts.prefetcher.fetch(num);delete num;},3000);
							}
						},
						timeout:60000,
						type:"POST",
						url:tts.config.prefetch.FETCH_URL
					});//ajax end
				}
			}
		},
		/*CACHE CODE - need to overhaul this function to do caching and dirty cache/dirty address failover*/
		/*CACHE CODE - need buildErrorSound*/
		//param sel is a jQuery span
		buildSound: function(sel){
                    
                   //var test = soundManager.canPlayMIME('wav');
                    //alert(test);
                    
			if (soundManager.enabled){
				//$("#xhtml").append('<p>sound manager enabled</p>');
				var soundURL;
				/*BROWSER HACK*/
				if (tts.config.cache.ie){
					//append query string so that IE will not use browser cache.
					soundURL = sel.data('file') + '?'+tts.config.cache.ieDirty;
					//solution to same phrase repeating causing failiure in IE
					tts.config.cache.ieDirty = tts.config.cache.ieDirty + 1;
				}
				else{
					soundURL = sel.data('file');
				}
				
				//soundURL = sel.data('file');
				//SoundManager2 specific code
				sel.
					data( 'sound', soundManager.createSound({
						id : 'sound' + sel.data('number'),
						url : soundURL,
						span : sel, //for referencing the span if scrolling is on.  may also help for highlite.
						/**alteration**/
						onload: function(){
							//$("#tts_init_message").append('<p>sound onload fired ' + sel.data('sound').readyState + ' '+sel.data('file')+'</p>');
							//SoundManager2 sounds have load states which are checked here.
							if ( sel.data('sound').readyState == 2 ){
								//destroy the sound and build it again.
								//$("#xhtml").append('<p>sound failed to load</p>');
								sel.data('sound').destruct();
								sel.removeData('sound');					
								
								if(sel.data('error_sound_loaded')){
									return;
								}
								//load null sound if span has repeatedly failed to acquire mp3
								if (sel.data('dirty_mp3') === true){
									sel.data('bad_mp3_url',sel.data('file'));
									var errorURL;
									if (tts.config.cache.ie){
										errorURL = tts.config.prefetch.ERROR_SOUND_URL + '?' +tts.config.cache.ieDirty;
									}
									else{
										errorURL = tts.config.prefetch.ERROR_SOUND_URL;
                                                                                
									}
									sel.data('file',errorURL);
									sel.data('error_sound_loaded',true);
									sel.data('state',tts.config.prefetch.SPAN_THREW_ERROR);
									tts.prefetcher.buildSound(sel);
									return;
								}
								//check if storing
								if(tts.config.cache.enabled === true){
									if (sel.data('stored') === true){
										$.storage.del(sel.data('key'));
									}
								}
								sel.data('dirty_mp3',true);
								var timestamp = new Date().getTime();
								sel.data('file',sel.data('file') + timestamp);
								tts.prefetcher.buildSound(sel);					
								

							}
							else{
							//if span has failed completely, add the span to the error report.
								if(sel.data('error_sound_loaded')){
									//$("#xhtml").append('<p>error sound loaded</p>');
									//add to error report array
									tts.config.prefetch.errorSpans.push(sel.data('number'));
									//may want to check typeof and set it up if it does not exist?
									//will also need to reset this and error spans in wipespans and possibly in json config
									if (tts.config.prefetch.alreadyReporting === false){
										tts.config.prefetch.alreadyReporting = true;
										tts.prefetcher.reportErrors();
									}//call error report array
									return;
								}
								else{
									//$("#xhtml").append('<p>sound has loaded</p>');
									sel.data('state',tts.config.prefetch.SPAN_HAS_LOADED_SOUND);
									if(tts.config.cache.enabled === true){
										if (sel.data('stored') === false){
											$.storage.set( sel.data('key'), sel.data('file'));
											sel.data('stored',true);
										}
									}					
								}
							}
						},
						/** end alteration **/
						onfinish: function(){
							if ((tts.config.spans.currentSpan < tts.config.spans.spanCollection.length -1 ) && (tts.config.spans.currentSpan > -1)){								
								if(tts.config.spans.nextSpanToPlay == -1){
									tts.config.spans.nextSpanToPlay = sel.data('number');
									tts.config.spans.nextSpanToPlay++;								
								}
								tts.config.spans.playingSpans = 0;
								tts.spanController.playCurrentSpan();								
								
							}
							//for the last span.  it should pause everything instead of playing the next.
							else{

								tts.config.spans.nextSpanToPlay = sel.data('number');
								tts.spanController.pauseAll();
								
							}
						}
					} ) );	
			}
			else{//build a queue
				//$("#xhtml").append('<p>sound manager not enabled</p>');
				if (typeof tts.config.SM !== 'undefined' && typeof tts.config.SM.HAS_ONREADY !== 'undefined'){
					tts.config.SM.indices.push(sel.data('number'));
				}
				else{
					//set up an onready event for soundManager to make a sound for each item in queue
					$.extend(tts.config,{SM:{HAS_ONREADY:true,indices:[]}});
					soundManager.onready(function(){
						$.each( tts.config.SM.indices, function( index, value ) {
							tts.prefetcher.buildSound(tts.config.spans.spanCollection.eq(value));
						});
					});
				}
			
			}
		},
		reportErrors: function(){
			
			//prep things if necessary
			if(typeof tts.config.prefetch.firstErrorPass !== 'undefined' && tts.config.prefetch.firstErrorPass === true){
				if(typeof tts.config.prefetch.unfinishedSounds === 'undefined'){
					//instantiate
					$.extend(tts.config.prefetch,{
						unfinishedSounds:[]
					});
				}
				var unfinished = [];
				for (i=0;i<tts.config.spans.spanCollection.length;i++){
					var $span = tts.config.spans.spanCollection.eq(i);
					if ($span.data('state') < tts.config.prefetch.SPAN_HAS_LOADED_SOUND){
						unfinished.push = $span.data('number');
					}
				}
				$.extend(tts.config.prefetch,{
						unfinishedSounds:unfinished
					});
				tts.config.prefetch.firstErrorPass = false;
				setTimeout(tts.prefetcher.reportErrors,2000);
				return;
			}
			
			//traverse the unfinished sounds and update the array
			if(tts.config.prefetch.unfinishedSounds.length === 0){
				var unfinished = [];
				for (i=0;i<tts.config.prefetch.unfinishedSounds.length;i++){
					var n = tts.config.prefetch.unfinishedSounds[i];
					var $span = tts.config.spans.spanCollection.eq(n);
					if ($span.data('state') < tts.config.prefetch.SPAN_HAS_LOADED_SOUND){
						unfinished.push = $span.data('number');
					}
				}
				$.extend(tts.config.prefetch,{
					unfinishedSounds:unfinished
				});			
				if (unfinished.length === 0){
					var errors = tts.config.prefetch.errorSpans.length;
					if (errors !== 0){
						//ajax call to report to server.
						var datas = {
							"errors":errors,
							"thisURL":window.location.pathname
						};
						
						//collect all of the spans with status of 0.
						for (i=0;i<errors;i++){
							var $span = tts.config.spans.spanCollection.eq(tts.config.prefetch.errorSpans[i]);
							var property_file = "file_"+i;
							var property_text = "text_"+i;
							$.extend(datas,{
								property_file:$span.data('file'),
								property_text:$span.data('text_to_speak')
							});
						}
						
						$.ajax({
							asynch:false,
							cache:false,
							data:datas,
							global:false,
							dataType:'json',
							beforeSend: function(x) {
								if(x && x.overrideMimeType) {
									x.overrideMimeType("application/j-son;charset=UTF-8");
								}
							},
							timeout:60000,
							type:"POST",
							url:tts.config.prefetch.ERROR_REPORT_URL,
							error:function(){
								//possibly do variable reset here?
							},
							success:function(){
							}
						});//ajax end					
					}
				}
			}
			else {
				setTimeout(tts.prefetcher.reportErrors,2000);				
			}
		}
	},
	spanController:{
		changeCurrentSpan:function(){
			//this overrides the next span called by onfinish
			
			if(tts.config.spans.nextSpanToPlay != -1){
				tts.config.spans.previousSpan = tts.config.spans.currentSpan;
				tts.config.spans.currentSpan = tts.config.spans.nextSpanToPlay;
				tts.config.spans.nextSpanToPlay = -1;			
			}
			//if it has sound, reset it and pause it
			//could put in a previousSpan != -1 clause
			if(tts.config.spans.previousSpan != -1){
				if(tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).data('state') == tts.config.prefetch.SPAN_HAS_LOADED_SOUND){
					tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).data('sound').setPosition(0);
					tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).data('sound').stop();
				}
				//if it has highlite/loading classes, remove them
				if(tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).hasClass(tts.config.spans.highliteClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).removeClass(tts.config.spans.highliteClass);
				}
				if(tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).hasClass(tts.config.spans.loadingClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.previousSpan).removeClass(tts.config.spans.loadingClass);
				}
			}
			//if span has a loaded sound
			if(tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('state') == tts.config.prefetch.SPAN_HAS_LOADED_SOUND){
				//remove the loading class if it is present
				if(tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).hasClass(tts.config.spans.loadingClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).removeClass(tts.config.spans.loadingClass);
				}
				//add the highlite class if it is not
				if(!tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).hasClass(tts.config.spans.highliteClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).addClass(tts.config.spans.highliteClass);
				}
				//reset the span position
				tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('sound').setPosition(0);
				tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('sound').stop();
			}
			else{
				//add the loading class if it exists
				if(!tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).hasClass(tts.config.spans.loadingClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).addClass(tts.config.spans.loadingClass);
				}
				//remove the highlite class if it exists. this should not happen.
				if(tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).hasClass(tts.config.spans.highliteClass)){
					tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).addClass(tts.config.spans.highliteClass);
				}						
			}	
		},
		playCurrentSpan:function(){
			
			if (typeof tts.config.spans.spanCollection !== 'undefined' && tts.config.spans.spanCollection.length > 0){
				
				if (tts.config.spans.playingSpans < tts.config.spans.maxSimultaneousSpans){
					tts.spanController.changeCurrentSpan();
					if(tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('state') == tts.config.prefetch.SPAN_HAS_LOADED_SOUND){
						tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('sound').play();
						tts.config.spans.playingSpans = 1;
						//console.log('now playing span: '+tts.config.spans.currentSpan);
					}
				}
			}
		},
		pauseAll:function(){
			if (soundManager.enabled){
				//tts.config.spans.spanCollection.eq(tts.config.spans.currentSpan).data('sound').pause();
				soundManager.stopAll();
				if (tts.config.spans.playingSpans == 1){
					tts.config.spans.playingSpans = 0;
				}

				
			}
		},
		skip:function(n){
			//change the currentSpan and call playCurrentSpan
			if (typeof tts.config.spans.spanCollection !== 'undefined'){
				tts.spanController.pauseAll();
				if (((n + tts.config.spans.currentSpan) < tts.config.spans.spanCollection.length)&&((n + tts.config.spans.currentSpan) > -1)){
					tts.config.spans.nextSpanToPlay = n + tts.config.spans.currentSpan;

					notWaitingToPlayCurrent = true;

					tts.spanController.playCurrentSpan();
					return true;
				}
				else{
					return false;
				}
			}
		},
		setVolume : function(n){
			if (!soundManager.enabled){return;}
			if (typeof tts.config.spans.spanCollection ==='undefined'){return;}
			
			//change volume property of the global object.
			if (n >= 0 && n <= 100){
				soundManager.defaultOptions.volume = n;
			}
			tts.config.spans.spanCollection.each(function(){

				$this = $(this);
				if (typeof $this.data('sound') !== 'undefined'){
					$this.data('sound').setVolume(soundManager.defaultOptions.volume);
				}
			});
		},
		seekVolume: function(n){
			if (!soundManager.enabled){return;}
			if (typeof tts.config.spans.spanCollection ==='undefined'){return;}
			if((soundManager.defaultOptions.volume + n <= 100) &&(soundManager.defaultOptions.volume + n >= 0)){
				tts.spanController.setVolume(soundManager.defaultOptions.volume + n);
				$( "#tts_volume_slider" ).slider('value', soundManager.defaultOptions.volume + n);
			}
		},
                
		mute : function(){
			if (!soundManager.enabled){return;}
			if (typeof tts.config.spans.spanCollection ==='undefined'){return;}
			
			//if currently muted, unmute everything.
			if(soundManager.defaultOptions.globalMute){

				soundManager.defaultOptions.globalMute = false
				tts.spanController.setVolume(soundManager.defaultOptions.premute);
                                $( "#tts_volume_slider" ).slider('value', soundManager.defaultOptions.premute);
			}
			else{
			//save the premute volume and set the volume of all of the sound objects to 0

				soundManager.defaultOptions.globalMute = true;
				soundManager.defaultOptions.premute = soundManager.defaultOptions.volume;
				tts.spanController.setVolume(0);
                                $( "#tts_volume_slider" ).slider('value', 0);
			}
		}
	},
	//should move UI to the controller file
	UI:{
		//with all of these, if sm is not configured and configuration is false, do not fire.

		play : function(){
		if (typeof tts.config.TTS.TTSConfigured ==='undefined'){return;}
			tts.spanController.playCurrentSpan();
		},
		pause : function(){
		if (typeof tts.config.TTS.TTSConfigured ==='undefined'){return;}
			tts.spanController.pauseAll();
		},
		skipBack : function(){
			tts.spanController.skip(-1);
		},
		skipForward: function(){
			tts.spanController.skip(1);
		},
		volumeUp:function(){
			tts.spanController.seekVolume(5);
		},
		volumeDown:function(){
			tts.spanController.seekVolume(-5);
		},
		toggleMute:function(){
			tts.spanController.mute();
		},
                setVolume:function(){
                    tts.spanController.setVolume( $( "#tts_volume_slider" ).slider('value') );
                }
	}
};