<?php
/**
 * Plugin Name: WordPress weather information
 * Version: 1.0.0
 * Author: Nahid Ferdous Mohit
 * Author URI: https://nahid.dev
 * Plugin URI: https://nahid.dev
 */

final class WP_Weather_Info {

	protected $weather;
	protected $weather_widget;

	public function __construct() {
		if ( ! class_exists( 'Weather' ) ) {
			require plugin_dir_path( __FILE__ ) . 'Weather.php';
		}

		$this->weather = new Weather();

		if ( ! class_exists( 'Weather_Widget' ) ) {
			require plugin_dir_path( __FILE__ ) . 'Weather_Widget.php';
		}

		$this->weather_widget = new Weather_Widget( $this->weather );

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	public function register_widget() {
		register_widget( $this->weather_widget );
	}
}

new WP_Weather_Info();
