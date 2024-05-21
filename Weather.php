<?php

class Weather {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
	}

	public function init() {
		wp_add_dashboard_widget(
			'wdac_weather_widget',
			'Weather in Dhaka',
			array( $this, 'weather_widget_content' )
		);
	}

	public function weather_widget_content() {
		echo $this->get_weather_information();
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
}
