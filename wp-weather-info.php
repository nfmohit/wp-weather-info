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

	public function __construct() {
		if ( ! class_exists( 'Weather' ) ) {
			require plugin_dir_path( __FILE__ ) . 'Weather.php';
		}

		$this->weather = new Weather();

		add_action( 'send_email_hook', array( $this, 'send_email' ) );
		add_filter( 'cron_schedules', array( $this, 'custom_cron_intervals' ) );

		add_action( 'init', array( $this, 'init' ) );

		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	public function init() {
		if ( ! wp_next_scheduled( 'send_email_hook' ) ) {
			// hourly, twice_daily, daily, weekly
			wp_schedule_event( time(), 'every_two_minutes', 'send_email_hook' );
		}
	}

	public function custom_cron_intervals( $schedules ) {
		$schedules['twice_hourly'] = array(
			'interval' => 1800,
			'display'  => esc_html__( 'Twice Hourly', 'textdomain' ),
		);

		$schedules['every_two_minutes'] = array(
			'interval' => 120,
			'display'  => esc_html__( 'Every Two Minutes', 'textdomain' ),
		);

		return $schedules;
	}

	public function send_email() {
		$to = get_option( 'admin_email' );
		$subject = 'Current weather in Dhaka';
		$message = $this->weather->get_weather_information();
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);
		$sent = wp_mail( $to, $subject, $message, $headers );

		if ( $sent ) {
			error_log( 'Email sent successfully!' );
		} else {
			error_log( 'Failed to send email.' );
		}
	}

	public function deactivate() {
		wp_clear_scheduled_hook( 'send_email_hook' );
	}
}

new WP_Weather_Info();
