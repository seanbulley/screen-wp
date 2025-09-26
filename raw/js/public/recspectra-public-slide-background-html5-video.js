var recspectra_slide_bg_html5_video_selector = '.recspectra-slide-background-html5-video';

/**
 * Sets up the HTML5 Video slide background public functionality.
 *
 * @since	1.6.0
 * @since	1.7.2	Made sure binding of ticker events also happens when our view does not include
 *					HTML5 Video backgrounds, as they could be added later on, or included on a channel
 *					the display is later switched to. Fixes #31.
 */
jQuery(document).ready(function() {

	// Bind events even though our view does not includes HTML5 Video slides (yet!)
	recspectra_slide_bg_html5_video_bind_ticker_events();
});

/**
 * Binds events to be able to start and stop video playback at the right time, and prevent advancing to the next slide.
 *
 * @since	1.6.0
 */
function recspectra_slide_bg_html5_video_bind_ticker_events() {

	jQuery('body').on('slides:before-binding-events', recspectra_slides_selector, function ( event ) {
		// The slides ticker is about to set up binding events
		// Bind the slides:next-slide event early so we can prevent its default action if we need to

		jQuery('body').on('slides:next-slide', recspectra_slides_selector, function( event ) {
			// The next slide event is triggered
			// Determine if we should prevent its default action or not

			// Set container
			var $container = jQuery(recspectra_slide_bg_html5_video_selector).filter('.active').find('.html5-video-container');

			// Set video reference
			var vid = $container.find('video').get(0);

			if (vid && 1 == $container.data('recspectra-hold-slide')) {
				// We should wait for the end of the video before proceeding to the next slide, but only when playing

				if ( ( vid.currentTime > 0 && !vid.paused && !vid.ended && vid.readyState > 2 ) ) {
					// Video is playing, maybe prevent next slide

					if ( recspectra_slide_bg_html5_video_is_almost_ended($container, vid) ) {
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

	jQuery('body').on('slide:became-active', recspectra_slide_bg_html5_video_selector, function( event ) {
		// A video slide became active

		// Activate the object-fit polyfill for browsers that do not support it object fit on videos (Edge..)
		objectFitPolyfill();

		// Set container
		var $container = jQuery(this).find('.html5-video-container');

		// Set video reference
		var vid = $container.find('video').get(0);

		if (vid) {

			// Set mute status
			if (! $container.data('recspectra-output-sound')) {
				// No sound (unless enable sound option is checked)
				vid.muted = true;
			}
			else {
				vid.muted = false;
			}

			// Seek to start position, but only after video is ready to receive commands
			vid.addEventListener('playing', function recspectra_slide_bg_html5_video_seek_to_start() {
				// Remove event listerner to avoid endless loop of triggered 'playing' events
				this.removeEventListener('playing', recspectra_slide_bg_html5_video_seek_to_start, false);
				this.currentTime = $container.data('recspectra-video-start');
			}, false);

			// Play video
			vid.play();
		}
	});

	jQuery('body').on('slide:left-active', recspectra_slide_bg_html5_video_selector, function( event ) {
		// A video slide left the active state

		// Set container
		var $container = jQuery(this).find('.html5-video-container');

		// Set video reference
		var vid = $container.find('video').get(0);

		if (vid) {

			// Pause video whenever CSS transitions are over
			setTimeout(function() {

				// Pause video
				vid.pause();

			}, recspectra_ticker_css_transition_duration * 1000);
		}
	});
}

/**
 * Checks if a video has almost ended.
 *
 * @since	1.6.0
 *
 * @param	jQuery		$container	The container of the video.
 * @param	HTMLElement	vid			The reference to our HTML5 video element.
 */
function recspectra_slide_bg_html5_video_is_almost_ended($container,vid) {

	if (vid) {

		var end = $container.data('recspectra-video-end');
		var duration = vid.duration;
		var current_time = vid.currentTime;

		if ( duration < end || !end ) {
			end = duration;
		}

		if ( current_time >= end - recspectra_ticker_css_transition_duration ) {
			// Video almost ended
			return true;
		}
	}

	return false;
}

