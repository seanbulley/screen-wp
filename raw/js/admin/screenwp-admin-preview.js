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

		var $preview_actions = $('.screenwp-preview-actions .screenwp-orientation-button');
		var $preview = $('.screenwp-preview');

		$preview_actions.on( 'click', function() {

			var orientation_choice = jQuery(this).attr('data-orientation');

			$preview_actions.removeClass('active');

			for( var orientation_key in screenwp_preview.orientations ) {
				$preview.removeClass( 'screenwp-preview-' + orientation_key );
			}

			$preview.addClass( 'screenwp-preview-'+orientation_choice );

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
			'action': 'screenwp_preview_save_orientation_choice',
			'orientation': orientation,
			'object_id' : screenwp_preview.object_id,
		};
		jQuery.post(screenwp_preview.ajax_url, data );
	}
})( jQuery );