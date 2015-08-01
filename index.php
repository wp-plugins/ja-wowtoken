<?php
/*
Plugin Name: ja WowToken
Description: WordPress Widget that shows the latest prices from <a href="http://www.wowtoken.info">WowToken.info</a>.  This plugin is not affiliated with WowToken.info in any way.
Plugin URI: http://www.johnaldred.com/ja-wowtoken/
Author: John Aldred
Author URI: http://www.johnaldred.com/
Version: 1.0.1
License: GPL2
*/

/*

    Copyright (C) 2015  John Aldred  http://www.johnaldred.com/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// Creating the widget 
class jaWowToken_widget extends WP_Widget {

	// The JSON feed URL
	var $_json_feed = 'https://wowtoken.info/wowtoken.json';

	// Max age of data, in seconds.  WowToken.info terms specify that requests
	// should be no more frequently than once every 10 minutes (600 seconds).
	var $_interval = 900;

	// The Regions
	var $_regions = array(
		'NA' => 'North America',
		'EU' => 'Europe',
		'CN' => 'China',
		'TW' => 'Taiwan',
		'KR' => 'Korea',
	);

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'jaWowToken_widget', 

		// Widget name will appear in UI
		__('WowToken.info', 'jaWowToken_widget_domain'), 

		// Widget description
		array( 'description' => __( 'WowToken.info Widget', 'jaWowToken_widget_domain' ), ) 
		);
	}

	private function _getFeed($lasttime) {
		$data = array();
		// Check if $lasttime is longer ago than 15 minutes.
		if ((current_time('timestamp') - $lasttime) > $this->_interval) {
			// If it is, pull the data from the website and save it to the database and
			$feedData = wp_remote_get($this->_json_feed);

			if ($feedData['response']['code'] == 200) {

				$tmpData = json_decode($feedData['body']);
				$tmpData = $tmpData->update;

				$data = [
					'NA' => $tmpData->NA->raw->buy,
					'EU' => $tmpData->EU->raw->buy,
					'CN' => $tmpData->CN->raw->buy,
					'TW' => $tmpData->TW->raw->buy,
					'KR' => $tmpData->KR->raw->buy
				];

				// Encode the data and save it out to the database.
				$savedata = json_encode($data);
				update_option('jawowtokendata', $savedata);
				update_option('jawowtokentime', current_time('timestamp'));
			} else {
				$data = json_decode(get_option('jawowtokendata'), true);
			}
		} else {
			$data = json_decode(get_option('jawowtokendata'), true);
		}
		return $data;
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$lasttime = get_option('jawowtokentime') ? get_option('jawowtokentime') : (current_time('timestamp') - 901);
		$feedData = $this->_getFeed($lasttime);

		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		$regionPrice = $feedData[$instance['region']];
		// $regionPrice displays the actual gold value of tokens in the chosen region.

		echo __('<div class="jawowtoken_text">Region : '.$this->_regions[$instance['region']].'</div>');
		echo __('<div class="jawowtoken_price"><img src="' . plugins_url( 'goldicon.png', __FILE__ ) . '" > ');
		echo __(number_format($regionPrice).'</div>', 'jaWowToken_widget_domain' );

		if ((current_time('timestamp') - get_option('jawowtokentime')) > 0) {
			$ago = current_time('timestamp') - get_option('jawowtokentime');
			$agomin = (date(i, $ago) > 0) ? ltrim(date(i, $ago), 0) .' min ' : '';
			$agosec = (date(s, $ago) > 0) ? ltrim(date(s, $ago), 0) .' sec' : '0 sec';
			$ago = 'Updated '.$agomin.$agosec.' ago';
		} else {
			$ago = 'Just updated';
		}

		if ($instance['wtlink'] == 'true') {
			$wtlink = '<a href="http://www.wowtoken.info" target="_blank">WowToken.info</a>';
		} else {
			$wtlink = 'WowToken.info';
		}

		echo __('<div class="jawowtoken_credit"><o>'.$ago.', via '.$wtlink.'</p></div>');

		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
			$region = $instance[ 'region' ];
		} 	else {
			$title = __( 'WoWToken.info', 'jaWowToken_widget_domain' );
			$region = 'NA';
		}

	// Widget admin form
	?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'region' ); ?>"><?php _e( 'Choose Region : ' ); ?></label>
<select class="widefat" name="<?php echo $this->get_field_name('region'); ?>" id="">
<?php
	$output = '';
	foreach ($this->_regions as $option=>$value) {
		$selected = ($option == $instance['region']) ? ' selected' : '';
		$output .= '<option'.$selected.' value="'.$option.'">'.$value.'</option>'."\n";
	}
	echo $output;
?>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id( 'wtlink' ); ?>"><?php _e( 'Link WowToken.info : ' ); ?></label>
<select class="widefat" name="<?php echo $this->get_field_name('wtlink'); ?>" id="">
	<option <?php echo ($instance['wtlink'] == 'true') ? 'selected ' : ''; ?>value="true">Yes</option>
	<option <?php echo ($instance['wtlink'] == 'false') ? 'selected ' : ''; ?>value="false">No</option>
</select>
</p>
	<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['region'] = ( ! empty( $new_instance['region'] ) ) ? strip_tags( $new_instance['region'] ) : '';
		$instance['wtlink'] = ( ! empty( $new_instance['wtlink'] ) ) ? strip_tags( $new_instance['wtlink'] ) : '';
		return $instance;
	}

} // Class jaWowToken_widget ends here

// Register and load the widget
add_action( 'widgets_init', function() {
	register_widget( 'jaWowToken_widget' );
});

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_style( 'myprefix-style', plugins_url('jawowtoken.css', __FILE__) );
});

add_action('activated_plugin', function() {
	$feedData = wp_remote_get('https://wowtoken.info/wowtoken.json');

	if ($feedData['response']['code'] == 200) {

		$tmpData = json_decode($feedData['body']);
		$tmpData = $tmpData->update;

		$data = [
			'NA' => $tmpData->NA->raw->buy,
			'EU' => $tmpData->EU->raw->buy,
			'CN' => $tmpData->CN->raw->buy,
			'TW' => $tmpData->TW->raw->buy,
			'KR' => $tmpData->KR->raw->buy
		];

		// Encode the data and save it out to the database.
	} else {
		$data = [
			'NA' => '0',
			'EU' => '0',
			'CN' => '0',
			'TW' => '0',
			'KR' => '0'
		];
	}
	$savedata = json_encode($data);
	update_option('jawowtokendata', $savedata);
	update_option('jawowtokentime', current_time('timestamp'));

});

add_action('deactivated_plugin', function() {
	delete_option('jawowtokentime');
	delete_option('jawowtokendata');
});

?>