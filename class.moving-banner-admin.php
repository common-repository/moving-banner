<?php

class MovingBanner_Admin {
	protected static $instance = NULL;

	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}

	public function init() {
	    if ( (is_user_logged_in() && current_user_can('administrator'))) {
            load_plugin_textdomain( 'moving-banner', false, 'moving-banner/languages' );
            $this->init_hooks();
	    }
	}

	public function init_hooks() {
		add_action( 'admin_init', array( MovingBanner_Admin::get_instance(), 'admin_init' ) );
		add_action( 'admin_menu', array(
			MovingBanner_Admin::get_instance(),
			'admin_menu'
		), 5 ); # Priority 5, so it's called before Jetpack's admin_menu.
		add_action( 'admin_enqueue_scripts', array( MovingBanner_Admin::get_instance(), 'load_assets' ) );
	}

	public function admin_init() {
		load_plugin_textdomain( 'movingbanner' );
		register_setting( 'moving_banner_settings', 'mb_settings_array' );
		add_settings_section( 'moving_banner_configuration', __('Configuration', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_configuration_section_render'
		), 'moving-banner' );
		add_settings_field( 'moving_banner_height', __('Bar height', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_configuration_height_field_render'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_status', __('Enable/disable', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_configuration_enable_status_field_render'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_link', __('Target link', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_configuration_link_field_render'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_animation_speed', __('Animation speed (in seconds, e.g 1 fast, 10 slow, 30 super slow)', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_animation_speed_field_render'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_animation_direction', __('Animation direction', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_configuration_animation_direction_field_render'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_start_datetime', __('Start datetime', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_start_datetime'
		), 'moving-banner', 'moving_banner_configuration' );

		add_settings_field( 'moving_banner_end_datetime', __('End datetime', 'moving-banner'), array(
			MovingBanner_Admin::get_instance(),
			'on_moving_banner_end_datetime'
		), 'moving-banner', 'moving_banner_configuration' );

	}

	public function admin_menu() {
		$this->load_menu();
	}

	public static function load_menu() {
		add_menu_page( __('Moving Banner Settings', 'moving-banner'), __('Moving Banner Settings', 'moving-banner'), 'manage_options', 'moving-banner-settings', array(
			MovingBanner_Admin::get_instance(),
			'load_plugins_options'
		) );
	}

	public function on_moving_banner_configuration_section_render() {
		echo __( 'Provide your own custom settings for the moving banner plugin', 'moving-banner' );
	}

	public function on_moving_banner_start_datetime() {
		$moving_banner_start_datetime = $this->get_plugin_setting("moving_banner_start_datetime");
		if (is_null($moving_banner_start_datetime)) {
			?>
            <input type='text' name='mb_settings[moving_banner_start_datetime]' id="mb_date_timepicker_start">
			<?php
		} else {
			?>
            <input type='text' name='mb_settings[moving_banner_start_datetime]' id="mb_date_timepicker_start"
                   value='<?php echo $this->convert_to_local_from_unix_date($moving_banner_start_datetime); ?>'>
			<?php
		}
	}

	public function on_moving_banner_end_datetime() {
		$moving_banner_end_datetime = $this->get_plugin_setting("moving_banner_end_datetime");
		if (is_null($moving_banner_end_datetime)) {
			?>
            <input type='text' name='mb_settings[moving_banner_end_datetime]' id="mb_date_timepicker_end">
			<?php
		} else {
			?>
            <input type='text' name='mb_settings[moving_banner_end_datetime]' id="mb_date_timepicker_end"
                   value='<?php echo $this->convert_to_local_from_unix_date($moving_banner_end_datetime); ?>'>
			<?php
		}
	}

	public function on_moving_banner_configuration_animation_direction_field_render() {
		$moving_banner_animation_direction = $this->get_plugin_setting("moving_banner_animation_direction");
		if ( is_null($moving_banner_animation_direction) ) {
			?>
            <label>
                <input type='radio' name='mb_settings[moving_banner_animation_direction]' checked value='left'>
				<?php echo __( 'Left', 'moving-banner' ); ?>
            </label>
            <label>
                <input type='radio' name='mb_settings[moving_banner_animation_direction]'  value='right'>
				<?php echo __( 'Right', 'moving-banner' ); ?>
            </label>
			<?php
		} else {
			?>
                <label>
                    <input type='radio' name='mb_settings[moving_banner_animation_direction]' <?php checked( $moving_banner_animation_direction == "left" ? 1 : 0, 1 ); ?> value='left'>
                    <?php echo __( 'Left', 'moving-banner' ); ?>
                </label>
                <label>
                    <input type='radio' name='mb_settings[moving_banner_animation_direction]' <?php checked( $moving_banner_animation_direction == "right" ? 1 : 0, 1 ); ?> value='right'>
	                <?php echo __( 'Right', 'moving-banner' ); ?>
                </label>
            <?php
		}
	}

	public function on_moving_banner_configuration_enable_status_field_render() {
		$moving_banner_status = $this->get_plugin_setting("moving_banner_status");
		if ( is_null($moving_banner_status) ) {
			?>
            <input type='checkbox' name='mb_settings[moving_banner_status]' value='0'>
			<?php
		} else {
			?>
            <input type='checkbox' name='mb_settings[moving_banner_status]' <?php checked( $moving_banner_status, 1 ); ?>
                   value='1'>
			<?php
		}
	}

	public function on_moving_banner_animation_speed_field_render( ) {
		$moving_banner_animation_speed = $this->get_plugin_setting("moving_banner_animation_speed");
		if ( is_null($moving_banner_animation_speed) ) {
			?>
            <input type='text' name='mb_settings[moving_banner_animation_speed]'
                   value='2'>
			<?php
		} else {
			?>
            <input type='text' name='mb_settings[moving_banner_animation_speed]'
                   value='<?php echo $moving_banner_animation_speed; ?>'>
			<?php
		}
	}

	public function on_moving_banner_configuration_link_field_render( ) {
		$moving_banner_link = $this->get_plugin_setting("moving_banner_link");
		if ( is_null($moving_banner_link) ) {
			?>
            <input type='text' name='mb_settings[moving_banner_link]' placeholder="<?php echo __( 'http://link-to-advert.com', 'moving-banner' ) ?>"
                   value=''>
			<?php
		} else {
			?>
            <input type='text' name='mb_settings[moving_banner_link]'
                   value='<?php echo $moving_banner_link; ?>'>
			<?php
		}
	}

	public function on_moving_banner_configuration_height_field_render( ) {
		$moving_banner_height = $this->get_plugin_setting("moving_banner_height");
		if ( is_null($moving_banner_height) ) {
			?>
            <input type='text' name='mb_settings[moving_banner_height]'
                   value='40'>
			<?php
		} else {
			?>
            <input type='text' name='mb_settings[moving_banner_height]'
                   value='<?php echo $moving_banner_height; ?>'>
			<?php
		}
	}

	public function load_plugins_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$this->handle_save_settings();
		$options = get_option('mb_settings_array');
		?>
        <form method="post">
			<?php settings_fields( 'moving_banner_settings' ); ?>
			<?php do_settings_sections( 'moving-banner' ); ?>
            <div class='image-preview-wrapper'>
                <img id='image-preview'
                     src='<?php echo wp_get_attachment_url( $options['image_attachment_id'] ); ?>'
                     width='100' height='100' style='max-height: 200px; width: auto;'>
            </div>
            <input id="mb_upload_image_button" type="button" class="button" value="<?php echo __( 'Add banner', 'moving-banner'); ?>"/>
            <input type='hidden' name='mb_settings[image_attachment_id]' id='image_attachment_id'
                   value='<?php echo $options['image_attachment_id']; ?>'>
            <input type='hidden' name='mb_settings[user_time_zone]' id='user_time_zone'
                   value=''>
			<?php submit_button( __('Save', 'moving-banner')); ?>
        </form>
		<?php
	}

	public function get_plugin_setting($option_name) {
		$options = get_option( 'mb_settings_array' );
		if (is_null($options) || !array_key_exists($option_name, $options)) {
		    return null;
        }
        return $options[$option_name];
    }

	public function load_assets( $hook ) {
	    if($hook == "toplevel_page_moving-banner-settings") {
		    wp_enqueue_style( 'jquery.datetimepicker.min.css',
			    plugins_url( '/css/jquery.datetimepicker.min.css', __FILE__ ) );
		    wp_enqueue_script( 'jquery.datetimepicker.full.min.js',
			    plugins_url( '/js/jquery.datetimepicker.full.min.js', __FILE__ ) );
		    wp_enqueue_script( 'moving-banner-js',
			    plugins_url( '/js/moving-banner.js', __FILE__ ) );

		    $languageInfo = array(
			    'language' => $this->get_local_slug()
		    );

		    wp_localize_script( 'moving-banner-js', 'language_info', $languageInfo );
		    wp_enqueue_media();
	    }
	}

	public function handle_save_settings() {
		if ( isset( $_POST['mb_settings'] ) ) {
			$options = get_option('mb_settings_array');
			$post_data = $_POST['mb_settings'];
			$validation_result = $this->validate_fields($post_data);

			if (!empty($validation_result->errors)) {
				$error_messages = $validation_result->get_error_messages();
				foreach($error_messages as $error) {
					add_settings_error(
						'mb_settings_array_info',
						esc_attr( 'settings_updated' ),
						$error,
						'error'
					);
				}
				settings_errors( 'mb_settings_array_info' );
				return;
			}

            $options['image_attachment_id'] = intval( $post_data['image_attachment_id']);
            $options['moving_banner_height'] = intval( $post_data['moving_banner_height']);
			$options['moving_banner_link'] = sanitize_text_field( $post_data['moving_banner_link']);
			$options['moving_banner_animation_speed'] = floatval( $post_data['moving_banner_animation_speed']);

			$options['moving_banner_status'] = $this->get_status_flag($post_data);
			$options['moving_banner_animation_direction'] = sanitize_text_field( $post_data['moving_banner_animation_direction']);

			$options['moving_banner_start_datetime'] = $this->convert_local_date_to_unix_format($post_data['moving_banner_start_datetime']);
			$options['moving_banner_end_datetime'] = $this->convert_local_date_to_unix_format($post_data['moving_banner_end_datetime']);
			$options['moving_banner_user_timezone'] = intval($post_data['user_time_zone']);


			update_option( 'mb_settings_array', $options );

			$message = __( 'Successfully saved', 'moving-banner' );
			add_settings_error(
				'mb_settings_array_info',
				esc_attr( 'settings_updated' ),
				$message,
				'updated'
			);
			settings_errors( 'mb_settings_array_info' );
		}
	}

	private function get_status_flag($post_data) {
		if(array_key_exists('moving_banner_status', $post_data)) {
			return ENABLED;
		}
		else {
			return DISABLED;
		}
    }

    private function validate_fields($post_data) {
	    $errors = new WP_Error();
	    if ( isset( $post_data[ 'image_attachment_id' ] ) && $post_data[ 'image_attachment_id' ] === "0" ) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid banner image.', 'moving-banner') );
	    }

	    if ( isset( $post_data[ 'moving_banner_height' ] ) && $post_data[ 'moving_banner_height' ] === '' ) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid banner height.', 'moving-banner') );
	    }

	    if ( isset( $post_data[ 'moving_banner_link' ] ) && $post_data[ 'moving_banner_link' ] === '' ) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid banner link.', 'moving-banner') );
	    }

	    if ( isset( $post_data[ 'moving_banner_animation_speed' ] )
             && $post_data[ 'moving_banner_animation_speed' ] === ''
             || floatval($post_data[ 'moving_banner_animation_speed' ]) < 0) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid banner animation speed. (more or equal than 0)', 'moving-banner') );
	    }

	    if ( isset( $post_data[ 'moving_banner_start_datetime' ] ) && $post_data[ 'moving_banner_start_datetime' ] === '' ) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid start date time.', 'moving-banner') );
	    }

	    if ( isset( $post_data[ 'moving_banner_end_datetime' ] ) && $post_data[ 'moving_banner_end_datetime' ] === '' ) {
		    $errors->add( 'not-set', __('Unable to save the data, you have to provide a valid end date time.', 'moving-banner') );
	    }

	    return $errors;
    }

    private function convert_local_date_to_unix_format($datetime) {
	    $dateObject = null;
	    if($this->get_local_slug() == "en") {
		    $dateObject = DateTime::createFromFormat( "m/d/Y h:i a", $datetime );
	    }
	    else {
		    $dateObject = DateTime::createFromFormat( "d/m/Y H:i", $datetime );
	    }

	    return $dateObject->getTimestamp();
    }

    private function convert_to_local_from_unix_date($unix_datetime) {
	    $dateObject = new DateTime();
	    $dateObject->setTimestamp($unix_datetime);
	    if($this->get_local_slug() == "en") {
	        return $dateObject->format("m/d/Y h:i a");
	    }
	    else {
		    return $dateObject->format("d/m/Y H:i" );
	    }
    }

    private function get_local_slug() {
	    return substr(get_locale(), 0, 2 );
    }

}