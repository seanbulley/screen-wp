(function( $ ) {
	'use strict';

	 $(function() {
		/* DOM is ready */

		setup_preview_actions();

	});


	/**
	 * Activates the preview action buttons.
	 *
	 * @since	1.0.0
	 * @return void
	 */
	function setup_preview_actions() {

		var $preview_actions = $('.recspectra-preview-actions .recspectra-orientation-button');
		var $preview = $('.recspectra-preview');

		$preview_actions.on( 'click', function() {

			var orientation_choice = jQuery(this).attr('data-orientation');

			$preview_actions.removeClass('active');

			for( var orientation_key in recspectra_preview.orientations ) {
				$preview.removeClass( 'recspectra-preview-' + orientation_key );
			}

			$preview.addClass( 'recspectra-preview-'+orientation_choice );

			jQuery(this).addClass('active');

			save_orientation_choice( orientation_choice );
		});
	}

	/**
	 * Submits the user's orientation choice for the current Display, Channel or Slide.
	 *
	 * @since	1.0.0
	 * @return 	void
	 */
	function save_orientation_choice( orientation ) {
                var data = {
                        'action': 'recspectra_preview_save_orientation_choice',
                        'orientation': orientation,
                        'object_id' : recspectra_preview.object_id,
                        'nonce': recspectra_preview.nonce,
                };
		jQuery.post(recspectra_preview.ajax_url, data );
	}
})( jQuery );