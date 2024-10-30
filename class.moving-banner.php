<?php

class MovingBanner {

	public static function plugin_activation() {
		$dateNow = new DateTime();
		$dateNextWeek = new DateTime();
		date_add($dateNextWeek, date_interval_create_from_date_string("10 days"));

		self::plugin_deactivation();
		$settings = array( 'image_attachment_id'           => 0,
		                   'moving_banner_height'              => 40,
		                   'moving_banner_link'                => 'http://link-to-advert.com',
		                   'moving_banner_animation_speed'     => 2,
		                   'moving_banner_animation_direction' => 'left',
			               'moving_banner_status'              => 0,
			               'moving_banner_start_datetime' => $dateNow->getTimestamp(),
			               'moving_banner_end_datetime' => $dateNextWeek->getTimestamp(),
			               'moving_banner_user_timezone' => 0
		);
		add_option( 'mb_settings_array', $settings);
	}

	public static function plugin_deactivation() {
		delete_option( 'mb_settings_array' );
	}

}