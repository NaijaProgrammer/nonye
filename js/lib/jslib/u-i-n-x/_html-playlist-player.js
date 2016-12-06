function Playlist(config)
{
	config = config || {};
	
	//configuration parameters
	var playlist     = config.playlistID,
	    playlistItem = config.playlistItem, //e.g li, div, span, etc
		player       = config.playerID,
	    playButton   = config.playButtonID,
		nextButton   = config.nextButtonID,
		prevButton   = config.prevButtonID,
		stopButton   = config.stopButtonID
		
		
	//declare callbacks
	var playPauseCallback = config.playPauseCallback || function(playButton){},
	
	    //Called when the stop buttion is clicked, passed the play and stop buttons, as well as the HTML elements containing all songs
	    stopCallback = config.stopCallback || function(playButton, stopButton, songItems){},
	
        //This function is called for the currently playing song, it is passed the HTML element containing the current song	
	    currentSongCallback = config.currentSongCallback || function(currentSongItem){  },
	
	    //When current song is playing, this function will be called on the HTML elements containing all other songs not currently playing
	    nonPlayingSongCallback = config.nonPlayingSongCallback || function(songItem){  }
		
		
	//Declare non-configuration variables
	var songs        = [],
    songsList        = $O(playlist).querySelectorAll(playlistItem),
    songsLength      = songsList.length,
    currentSongIndex = 0;
    playlistPlayer   = $O(player);
    currentSongURL   = playlistPlayer.src;
	
	for(var i = 0; i < songsLength; i++)
	{
		songs.push( {'title':$Html(songsList[i]), 'url':songsList[i].getAttribute('data-url')} );
	}
	
	attachListener(playButton, 'click', function(e){
		if( decodeURI(playlistPlayer.src) == decodeURI(songs[currentSongIndex]['url']) )
		{
			togglePlayPause();
		}
		else
		{
			playlistPlayer.src = songs[currentSongIndex]['url'];
			togglePlayPause();
		}
	});
	
	attachListener(nextButton, 'click', function(e){
		playNextSong();
	});

	attachListener(prevButton, 'click', function(e){
		playPrevSong();
	});

	attachListener(stopButton,  'click', function(e){
		stop();
	});

	attachListener(playlistPlayer, 'ended', function(e){
		playNextSong();
	});

	attachListener(playlist, 'click', function(e){
		cancelDefaultAction(e);
		
		var specificTarget     = getEventTarget(e);
		var selectedAudioTitle = $Html(specificTarget);
		var selectedAudioURL   = specificTarget.getAttribute('data-url');
		
		if( decodeURI(currentSongURL) == decodeURI(selectedAudioURL) )
		{ 
			return;
		}
		else if(selectedAudioURL)
		{
			playlistPlayer.src = selectedAudioURL;
			togglePlayPause();
			
			currentSongCallback(specificTarget);
			for(var i = 0; i < songsLength; i++)
			{
				if( songsList[i] != specificTarget)
				{
					nonPlayingSongCallback(songsList[i]);
				}
			}
		}
	});

	
	//Define public functions
	function togglePlayPause()
	{
		if(playlistPlayer.paused)
		{
			applyCurrentSongCallback(currentSongIndex);
			playlistPlayer.play();
			playPauseCallback(playButton);
		}
		else
		{
			playlistPlayer.pause();
			playPauseCallback(playButton);
		}
	}
	
	function play(indx)
	{
		indx = indx || 0;
		playlistPlayer.src = songs[indx]['url'];
		applyCurrentSongCallback(indx);
		playlistPlayer.play();
	}
	
	function pause()
	{
		playlistPlayer.pause();
	}
	
	function applyCurrentSongCallback(currSongIndex)
	{
		currentSongCallback(songsList[currSongIndex]);
		for(var i = 0; i < songsLength; i++)
		{
			if( i != currSongIndex)
			{
				nonPlayingSongCallback(songsList[i]);
			}
		}
	}
	
	function playNextSong()
	{
		selectNextSong();
		togglePlayPause();
	}

	function playPrevSong()
	{
		selectPrevSong();
		togglePlayPause();
	}

	function selectNextSong()
	{
		currentSongIndex++;
		if(currentSongIndex >= songsLength)
		{
			currentSongIndex = 0;
		}
		playlistPlayer.src = songs[currentSongIndex]['url'];
	}

	function selectPrevSong()
	{
		currentSongIndex--;
		if(currentSongIndex < 0)
		{
			currentSongIndex = songsLength - 1;
		}
		playlistPlayer.src = songs[currentSongIndex]['url'];
	}
	
	function stop()
	{
		playlistPlayer.pause();
		currentSongIndex           = 0;
		playlistPlayer.currentTime = 0;
		stopCallback(playButton, stopButton, songsList);
	}

	
	//Assign public functions as public member methods
	this.play            = play;
	this.pause           = pause;
	this.togglePlayPause = togglePlayPause;
	this.playNextSong    = playNextSong;
	this.playPrevSong    = playPrevSong;
	this.stop            = stop;

	
	//Define Auxiliary private functions 
	function $O(id)
	{
		return ( (typeof id === 'object') ? id : document.getElementById(id) );
	}
	
	function $Html(id, value)
	{
		var elem = $O(id);
		
		if(value)
		{
			elem.innerHTML = value;
		}
		
		return elem.innerHTML;
	}
	
	function $Style(id)
	{
		return $O(id).style;
	}
	
	function addLoadListener(callback)
	{
		EventManager.addLoadListener(callback);
	}
	
	function attachListener(target, eventType, functionRef, capture)
	{
		EventManager.attachEventListener(target, eventType, functionRef, capture);
	}
	
	function detachListener(target, eventType, functionRef, capture)
	{
		EventManager.detachEventListener(target, eventType, functionRef, capture);
	}
	
	function cancelDefaultAction(e)
	{
		EventManager.cancelDefaultAction(e);
	}
	
	function stopPropagation(e)
	{
		EventManager.stopEventPropagation(e);
	}
	
	function getEventTarget(e)
	{
		return EventManager.eventTarget(e);
	}
}