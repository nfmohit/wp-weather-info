<?php
/**
 * Plugin Name: WordPress weather information
 * Version: 1.0.0
 * Author: Nahid Ferdous Mohit
 * Author URI: https://nahid.dev
 * Plugin URI: https://nahid.dev
 */

final class WP_Weather_Info {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
	}

	public function init() {
		wp_add_dashboard_widget(
			'wdac_weather_widget',
			'Weather in Dhaka',
			array( $this, 'weather_widget_content' )
		);

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$this->send_email();
		}
	}

	public function weather_widget_content() {
		echo $this->get_weather_information();
		?>
		<form method="post">
			<label for="email">Email:</label><br>
			<input type="email" name="email" id="email" placeholder="Email"><br>
			<input type="submit" value="Subscribe">
		</form>
		<?php
	}

	public function get_weather_information() {
		$api_url = 'https://api.open-meteo.com/v1/forecast?latitude=23.7104&longitude=90.4074&current=temperature_2m,wind_speed_10m,relative_humidity_2m&timezone=auto';

		$response = wp_remote_get( $api_url );

		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}

		$response_body   = wp_remote_retrieve_body( $response );
		$parsed_response = json_decode( $response_body, true );
		$current_weather = $parsed_response['current'];

		$html  = '<ul>';
		$html .= '<li>Time: ' . date( 'F j, Y, g:i a', strtotime( $current_weather['time'] ) ) . '</li>';
		$html .= '<li>Temperature: ' . $current_weather['temperature_2m'] . ' °C</li>';
		$html .= '<li>Wind Speed: ' . $current_weather['wind_speed_10m'] . ' km/h</li>';
		$html .= '<li>Humidity: ' . $current_weather['relative_humidity_2m'] . '%</li>';
		$html .= '</ul>';

		return $html;
	}

	public function send_email() {
		$email = sanitize_email( $_POST[ 'email' ] );

		if ( ! is_email( $email ) ) {
			return;
		}

		$subject = 'Weather in Dhaka';
		$message = $this->get_weather_information();
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		wp_mail( $email, $subject, $message, $headers );
	}

}

new WP_Weather_Info();
