<?php

class Weather_Widget extends WP_Widget {

	protected $weather;

	public function __construct( Weather $weather ) {
		$this->weather = $weather;

		parent::__construct(
			'wdac_weather_widget',
			'Weather in Dhaka',
			array(
				'description' => 'Display weather information in Dhaka',
			),
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo $this->weather->get_weather_information();
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo $instance['title'] ?? ''; ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
}
