var recspectra_yt_player;

/**
 * Sets up the Video slide format admin functionality.
 *
 * Functionality was copied from recspectra-admin-slide-video.js (since 1.2.0, removed in 1.4.0).
 *
 * @since	1.4.0
 * @since	1.5.1	Updates the player's mute status whenever the 'Enable sound?' checkbox is toggled.
 */
jQuery( function() {

	if (jQuery('#slide_bg_video_video_url').val() && jQuery('#slide_bg_video_video_url').val().length) {
		// YouTube video URL is set on load, validate it and load preview
		recspectra_admin_slide_bg_video_validate_youtube_video_url();
	}

	jQuery('#slide_bg_video_video_url').on('change', function() {
		// Validate changed YouTube video URL and load preview
		recspectra_admin_slide_bg_video_validate_youtube_video_url();
	});

	jQuery('#slide_bg_video_video_start').on('change', function() {
		// Update player with changed start time
		recspectra_admin_slide_bg_video_update_youtube_video_preview();
	});

	jQuery('#slide_bg_video_video_end').on('change', function() {
		// Update player with changed end time
		recspectra_admin_slide_bg_video_update_youtube_video_preview();
	});

	jQuery('#slide_bg_video_enable_sound').on('change', function() {
		// Update player's mute status
		recspectra_admin_slide_bg_video_update_player_mute();
	});

});

/**
 * Loads the YouTube IFrame Player API to be used in the Video format slide admin.
 *
 * @since	1.4.0
 */
function recspectra_admin_load_youtube_api() {
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
 * @since	1.5.1	Updates the player's mute status before playing.
 */
function recspectra_admin_slide_bg_video_prepare_player_for_playback() {
	if (window.recspectra_yt_player) {
		var player = window.recspectra_yt_player;
		recspectra_admin_slide_bg_video_update_player_mute();
		player.playVideo();
	}
}

/**
 * Updates the slide admin video preview player's mute status.
 *
 * Invoked whenever a video starts playing, or the mute property is toggled by the user.
 *
 * @since	1.5.1
 */
function recspectra_admin_slide_bg_video_update_player_mute() {
	if (window.recspectra_yt_player) {
		var player = window.recspectra_yt_player;

		if (jQuery('#slide_bg_video_enable_sound').prop('checked')) {
			player.unMute();
		}
		else {
			player.mute();
		}
	}
}

/**
 * Updates the slide admin video preview with new parameters as entered by the user, and restarts playback.
 *
 * @since	1.4.0
 * @since	1.5.1	Updates the player's mute status before playing.
 */
function recspectra_admin_slide_bg_video_update_youtube_video_preview() {
	if (jQuery('#slide_bg_video_video_id').val() && jQuery('#slide_bg_video_video_id').val().length) {
		// Video ID is set, update preview
		if (window.recspectra_yt_player) {
			var player = window.recspectra_yt_player;

			var video_id = jQuery('#slide_bg_video_video_id').val();
			var start = jQuery('#slide_bg_video_video_start').val();
			var end = jQuery('#slide_bg_video_video_end').val();

			if (video_id) {
				recspectra_admin_slide_bg_video_update_player_mute();
				player.loadVideoById( {videoId: video_id, startSeconds: start, endSeconds: end} );
			}
		}
		else {
			recspectra_admin_load_youtube_api();
		}
	}
}

/**
 * Validates the YouTube video URL entered by the user, and updated the preview player on success.
 *
 * @since	1.4.0
 */
function recspectra_admin_slide_bg_video_validate_youtube_video_url() {
	var video_metadata = recspectra_get_video_id(jQuery('#slide_bg_video_video_url').val());

	if (video_metadata && video_metadata.id && 'youtube' == video_metadata.service) {
		// Valid YouTube video URL, rewrite URL field and update the video preview
		jQuery('#slide_bg_video_video_url').val('https://youtu.be/' + video_metadata.id);
		jQuery('#slide_bg_video_video_id').val(video_metadata.id);

		jQuery('#slide_bg_video_video_url_notification').addClass('hidden');

		recspectra_admin_slide_bg_video_update_youtube_video_preview();
	}
	else {
		// Not a valid URL, pause video, empty video ID and show message
		if (window.recspectra_yt_player) {
			var player = window.recspectra_yt_player;
			player.pauseVideo();
		}
		jQuery('#slide_bg_video_video_id').val('');
		jQuery('#slide_bg_video_video_url_notification').removeClass('hidden');
	}
}

/**
 * Sets up the Video format slide admin player.
 *
 * Invoked whenever the YouTube IFrame Player API is ready.
 *
 * @since	1.4.0
 */
function onYouTubeIframeAPIReady() {

	var video_id = jQuery('#slide_bg_video_video_id').val();
	var start = jQuery('#slide_bg_video_video_start').val();
	var end = jQuery('#slide_bg_video_video_end').val();

	// Set up player and store its reference
	window.recspectra_yt_player = new YT.Player('recspectra-admin-video-preview', {
		width: '480',
		height: '270',
		videoId: video_id,
		playerVars: {
			'controls': 0,
			'modestbranding': 1,
			'rel': 0,
			'showinfo': 0,
			'start': start,
			'end': end,
		},
		events: {
			'onReady': recspectra_admin_slide_bg_video_prepare_player_for_playback,
		}
	});
}