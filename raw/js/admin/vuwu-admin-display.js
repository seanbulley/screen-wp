jQuery(document).ready(function() {

	vuwu_display_setup_channel_scheduler();

});

function vuwu_display_setup_channel_scheduler() {

	$start_datetime = jQuery('#vuwu_channel_editor_scheduled_channel_start');
	$end_datetime = jQuery('#vuwu_channel_editor_scheduled_channel_end');

	if (jQuery($start_datetime).length || jQuery($end_datetime).length) {

		// Only continue when datetime picker fields are present, datetimepicker will work with empty jQuery objects
		jQuery.vuwu_datetimepicker.setLocale(vuwu_channel_scheduler_defaults.locale);

		$start_datetime.vuwu_datetimepicker({
			format: vuwu_channel_scheduler_defaults.datetime_format,
			dayOfWeekStart : vuwu_channel_scheduler_defaults.start_of_week,
			step: 15,
			onChangeDateTime: function(start) {
				if (start) {
					if (!$end_datetime.val() || new Date($end_datetime.val()) < start) {
						var new_end = new Date(start.getTime() + vuwu_channel_scheduler_defaults.duration * 1000);
						// Uses https://plugins.krajee.com/php-date-formatter included with datetimepicker
						var fmt = new DateFormatter();
						$end_datetime.val(fmt.formatDate(new_end, vuwu_channel_scheduler_defaults.datetime_format));
					}
				}
			}
		});

		$end_datetime.vuwu_datetimepicker({
			format: vuwu_channel_scheduler_defaults.datetime_format,
			dayOfWeekStart : vuwu_channel_scheduler_defaults.start_of_week,
			step: 15
		});

	}
}
