var recspectra_slide_bg_video_selector = '.recspectra-slide-background-video';
var recspectra_yt_players = {};
var recspectra_yt_api_ready = false;

/**
 * Sets up the YouTube Video slide background public functionality.
 *
 * Functionality was copied from recspectra-public-slide-video.js (since 1.2.0, removed in 1.4.0).
 *
 * @since	1.4.0
 * @since	1.7.2	Made sure binding of events and loading YouTube API also happens when our view does not
 *					include YouTube backgrounds, as they could be added later on, or included on a channel
 *					the display is later switched to. Fixes #31.
 */
jQuery(document).ready(function() {

	// Bind events & load YouTube API even though our view does not include YouTube slides (yet!)
	recspectra_slide_bg_video_load_youtube_api();
	recspectra_slide_bg_video_bind_display_loading_events();
	recspectra_slide_bg_video_bind_ticker_events();
});

/**
 * Binds events to be able to set up video players in newly loaded slide groups and replaced channels.
 *
 * @since	1.4.0
 */
function recspectra_slide_bg_video_bind_display_loading_events() {

	jQuery('body').on('channel:replaced-channel', recspectra_channel_selector, function ( event ) {
		if (recspectra_yt_api_ready) {
			recspectra_slide_bg_video_init_video_placeholders();
			recspectra_slide_bg_video_cleanup_youtube_players();
		}
		else {
			recspectra_slide_bg_video_load_youtube_api();
		}
	});

	jQuery('body').on('slides:loaded-new-slide-group', recspectra_slides_selector, function ( event ) {
		if (recspectra_yt_api_ready) {
			recspectra_slide_bg_video_init_video_placeholders();
		}
		else {
			recspectra_slide_bg_video_load_youtube_api();
		}
	});

	jQuery('body').on('slides:removed-old-slide-group', recspectra_slides_selector, function ( event ) {
		recspectra_slide_bg_video_cleanup_youtube_players();
	});
}

/**
 * Binds events to be able to start and stop video playback at the right time, and prevent advancing to the next slide.
 *
 * @since	1.4.0
 * @since	1.5.5	Let the slideshow continue to next slide when the video is not playing. This prevents holding
 *					the slideshow indefinitely in case of network failure. Fixes #16.
 */
function recspectra_slide_bg_video_bind_ticker_events() {

	jQuery('body').on('slides:before-binding-events', recspectra_slides_selector, function ( event ) {
		// The slides ticker is about to set up binding events
		// Bind the slides:next-slide event early so we can prevent its default action if we need to

		jQuery('body').on('slides:next-slide', recspectra_slides_selector, function( event ) {
			// The next slide event is triggered
			// Determine if we should prevent its default action or not

			// Set container
			var container = jQuery(recspectra_slide_bg_video_selector).filter('.active').find('.youtube-video-container');

			// Set player reference
			var player = window.recspectra_yt_players[container.attr('id')]

			if (1 == container.data('recspectra-hold-slide')) {
				// We should wait for the end of the video before proceeding to the next slide, but only when playing

				if (player && typeof player.playVideo === 'function') {
					// Player exists and is ready
					var end = container.data('recspectra-video-end');
					var duration = player.getDuration();
					var current_time = player.getCurrentTime();

					if ( duration < end || !end ) {
						end = duration;
					}

					if ( 1 !== player.getPlayerState() ) {
						// Video not playing, do not prevent next slide
					}
					else if ( current_time >= end - recspectra_ticker_css_transition_duration ) {
						// Video almost ended, do not prevent next slide
					}
					else {
						// Not ended yet, prevent next slide
						event.stopImmediatePropagation();

						// Try again in 0.5 seconds
						setTimeout(function() {
							jQuery(recspectra_slides_selector).trigger('slides:next-slide');
						}, 0.5 * 1000);
					}
				}
			}
		});
	});

	jQuery('body').on('slide:became-active', recspectra_slide_bg_video_selector, function( event ) {
		// A video slide became active

		// Set container
		var container = jQuery(this).find('.youtube-video-container');

		// Set player reference
		var player = window.recspectra_yt_players[container.attr('id')]

		if (player && typeof player.playVideo === 'function') {
			// Player exists and is ready

			// Seek to start
			player.playVideo();
		}
	});

	jQuery('body').on('slide:left-active', recspectra_slide_bg_video_selector, function( event ) {
		// A video slide left the active state

		// Set container
		var container = jQuery(this).find('.youtube-video-container');

		// Set player reference
		var player = window.recspectra_yt_players[container.attr('id')]

		if (player && typeof player.playVideo === 'function') {
			// Player exists

			// Stop video whenever CSS transitions are over
			setTimeout(function() {
				player.seekTo(container.data('recspectra-video-start'));
				player.pauseVideo();
			}, recspectra_ticker_css_transition_duration * 1000);
		}
	});
}

/**
 * Cleans up unused YouTube player references.
 *
 * Used after newly loaded slide groups and replaced channels.
 *
 * @since	1.4.0
 * @since	1.5.5	Removed the resize event trigger for players that are no longer present.
 */
function recspectra_slide_bg_video_cleanup_youtube_players() {
	for (var player_id in window.recspectra_yt_players) {
		if (!jQuery('#' + player_id).length) {
			// Video is no longer present in the document, remove its player reference
			delete window.recspectra_yt_players[player_id];

			// Remove the resize event trigger for this player
			jQuery(window).off('resize', function() {
				recspectra_slide_bg_video_resize_youtube_to_cover(player_id);
			});
		}
	}
}

/**
 * Inits all new video placeholders, storing player references for later use.
 *
 * @since	1.4.0
 * @since	1.5.1	Sets a unique ID attribute for each container, and no longer relies on unique ID's
 *					coming from the server as this failed when page caching was enabled. Fixes issue #15.
 * @since	1.5.5	Added the 'playsinline' argument to encourage iOS to play YouTube background videos.
 *					Works! However not when in "Low Power Mode", and not for videos with sound enabled.
 */
function recspectra_slide_bg_video_init_video_placeholders() {
	// Loop over any video placeholders that are not yet replaced by an iframe
	jQuery('div.youtube-video-container').each(function() {

		// Set container
		var container = jQuery(this);

		// Set unique ID attribute
		container.attr('id', 'player-' + Math.random().toString(36).substr(2, 16));

		var player_id = container.attr('id');
		var video_id = container.data('recspectra-video-id');

		if (player_id && video_id) {
			// Set up player and store its reference
			window.recspectra_yt_players[player_id] = new YT.Player(player_id, {
				width: '1920',
				height: '1080',
				videoId: video_id,
				playerVars: {
					'controls': 0,
					'modestbranding': 1,
					'rel': 0,
					'showinfo': 0,
					'playsinline': 1,
				},
				events: {
					'onReady': recspectra_slide_bg_video_prepare_player_for_playback(player_id),
				}
			});
		}
	});
}

/**
 * Loads the YouTube IFrame Player API to be used in the Video format slide admin.
 *
 * @since	1.4.0
 */
function recspectra_slide_bg_video_load_youtube_api() {
	// Load YouTube IFrame Player API code asynchronously
	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
}

/**
 * Prepares the video so it is ready for playback.
 *
 * Invoked whenever the player is ready.
 *
 * @since	1.4.0
 * @since	1.5.1	Video slides no longer play when previewed while editing a Channel.
 *					Muting of video is now optional, based on the recspectra-output-sound data attribute.
 * @since	1.5.5	Invoked a method that resizes the YouTube player to cover the entire slide background
 *					with video. Also on window resize.
 *
 * @param	string	player_id	The ID of the player
 */
function recspectra_slide_bg_video_prepare_player_for_playback(player_id) {

	return function(event) {

		// Set container
		var container = jQuery('#' + player_id);

		// Set player reference
		var player = window.recspectra_yt_players[player_id];

		// Make sure YouTube player covers the entire slide background with video, also on window resize
		recspectra_slide_bg_video_resize_youtube_to_cover(player_id);
		jQuery(window).on('resize', function() {
			recspectra_slide_bg_video_resize_youtube_to_cover(player_id);
		});

		if ((window.self != window.top) && (top.location.href.search('/post.php?') != -1)) {
			// Viewed on a slide displayed within a Channel edit page: don't play video
			return;
		}

		if (!container.data('recspectra-output-sound')) {
			// No sound (unless enable sound option is checked)
			player.mute();
		}

		// Trigger buffering so video is ready to play when needed
		player.seekTo(container.data('recspectra-video-start'));

		if (
			jQuery(recspectra_slides_selector).length &&
			! jQuery('#' + player_id).parents(recspectra_slide_bg_video_selector).hasClass('active')
		) {
			// Viewed on a channel or display: When this video slide is not active at this very moment,
			// pause, so it can start playing whenever it becomes active
			player.pauseVideo();
		}
	}
}

/**
 * Resizes the YouTube player to cover the entire slide background with video.
 *
 * Invoked whenever a YouTube player is prepared for playback, and on window resize.
 *
 * YouTube video always has 16:9 aspect ratio, and is contained within the player iframe.
 * See: https://codepen.io/ccrch/pen/GgPLVW
 *
 * @since	1.5.5
 *
 * @param	string	player_id	The ID of the player
 */
function recspectra_slide_bg_video_resize_youtube_to_cover(player_id) {

	// Set container
	var container = jQuery('#' + player_id);

	// Set player reference
	var player = window.recspectra_yt_players[player_id];

	var	w = jQuery( window ).width() + 0,
		h = jQuery( window ).height() + 0;

	// Make the YouTube player 16:9 so the video covers the entire player,
	// and we can make the player cover the entire slide background
	if ( w/h > 16/9 ) {
		var new_h = w/16*9;
		player.setSize(w, new_h);
		container.css({'left': '0px'});
	}
	else {
		var new_w = h/9*16;
		player.setSize(new_w, h);
		container.css({'left': -(new_w-w)/2});
	}
}

/**
 * Marks the YouTube API as ready and inits placeholders.
 *
 * Invoked whenever the YouTube IFrame Player API is ready.
 *
 * @since	1.4.0
 */
function onYouTubeIframeAPIReady() {
	recspectra_yt_api_ready = true;
	recspectra_slide_bg_video_init_video_placeholders()
}
