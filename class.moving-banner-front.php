<?php

class MovingBanner_Front {
	private $settings;
	protected static $instance = NULL;

	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}

	public function init() {
		$this->settings = get_option( 'mb_settings_array' );

		if ( $this->is_bar_enabled() ) {
			$this->initialize_hooks();
			$this->show_advert_bar();
		}

	}

	function __construct() {
	}

	public function is_bar_enabled() {
		if ( $this->settings['moving_banner_status'] == ENABLED) {
			return true;
		} else {
			return false;
		}
	}

	public function show_advert_bar() {
		add_action( 'wp_head', array( MovingBanner_Front::get_instance(), 'inject_css_style' ) );
		add_action( 'wp_footer', array( MovingBanner_Front::get_instance(), 'put_advert_bar_before_page_footer' ) );
	}

	public function inject_css_style() {
	    $image_metadata = wp_get_attachment_metadata( $this->settings['image_attachment_id'] );
        $max_width = 5076;
        $calculated_container_width = floor($max_width / $image_metadata["width"]) * $image_metadata["width"];
		echo '<style>
            #moving-banner-container {
                overflow: hidden;
            }
            
            #moving-banner {
                position: fixed; 
                width: '.$calculated_container_width.'px; 
                height: ' . $this->settings['moving_banner_height'] . 'px; 
                bottom: 0px;
                z-index: 100;
                line-height: 0px;
                background: url( ' . wp_get_attachment_url( $this->settings['image_attachment_id'] ) . ') repeat-x;
                display: block;
                left: -'.$image_metadata["width"].'px;
            }
            .mb-marquee {
             overflow: hidden;
              -webkit-animation: marquee '.$this->settings['moving_banner_animation_speed'].'s linear infinite;
              animation: marquee '.$this->settings['moving_banner_animation_speed'].'s linear infinite;
            }
            
           '.$this->get_animation_keyframes_css($image_metadata).'
            </style>';
	}

	public function put_advert_bar_before_page_footer() {
	    if ($this->is_banner_enabled()) {
		    ?>
            <div id="moving-banner-container">
                <a id="moving-banner" class="mb-marquee" target="_blank"
                   href="<?php echo $this->settings['moving_banner_link']; ?>">
                </a>
            </div>
		    <?php
	    }
	}

	private function initialize_hooks() {
		wp_enqueue_media();
	}

	private function get_animation_keyframes_css($image_metadata) {
	    if($this->settings['moving_banner_animation_direction'] == "left") {
	        return '
	         @-webkit-keyframes marquee {
              0%{
                transform: translate3d(0, 0, 0);
              }
              100%{
                transform: translate3d(-'.$image_metadata["width"].'px, 0, 0);
              }
            }
            
            @keyframes marquee {
              0%{
                transform: translate3d(0, 0, 0);
              }
              100%{
                transform: translate3d(-'.$image_metadata["width"].'px, 0, 0);
              }
            }';
        }
        //return opposite (right direction) animation
        return '
          @-webkit-keyframes marquee {
              0%{
                transform: translate3d(0, 0, 0);
              }
              100%{
                transform: translate3d('.$image_metadata["width"].'px, 0, 0);
              }
            }
            
            @keyframes marquee {
              0%{
                transform: translate3d(0, 0, 0);
              }
              100%{
                transform: translate3d('.$image_metadata["width"].'px, 0, 0);
              }
            }';
    }

    private function is_banner_enabled() {
	    if ($this->settings['moving_banner_status'] == ENABLED
            && $this->is_current_date_between_start_and_end()) {
	        return true;
        }

        return false;
    }

    private function is_current_date_between_start_and_end() {
	    $dateObj = new DateTime('now', new DateTimeZone('UTC'));
	    $current_timestamp = $dateObj->getTimestamp() - ($this->settings['moving_banner_user_timezone'] * 60);
	    if($this->settings['moving_banner_start_datetime'] < $current_timestamp
           && $current_timestamp < $this->settings['moving_banner_end_datetime']) {
            return true;
	    }

	    return false;
    }
}