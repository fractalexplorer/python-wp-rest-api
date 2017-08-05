<?php
/* Template Name: Rpie */
if ( !is_super_admin() ) {
	wp_redirect( home_url() );
	exit;
}
get_header(); 

$mode = get_option( 'rpi_mode');
$twitter_mode = get_option( 'twitter_mode');
?>
<style>.rpie-wrapper{max-width:700px;width:100%;margin:20px auto;padding: 30px 0;}</style>
<div class="wrap rpie-wrapper">
	<form id="raspberrypi" enctype="multipart/form-data" method="post">
		<div id="primary" class="content-area">
			<h3>Manage Your Raspberry Pi settings here : </h3>	
			<label>Write your message :</label>
			<input type="text" placeholder="Write your message here..." id="call_python_text" value="<?php echo get_option( 'python_button_clicked');?>"/>
			<label>Call interval time in seconds :</label>
			<input type="text" placeholder="Call interval time in seconds..." id="call_python_time" value="<?php echo get_option( 'python_button_time');?>"/>
			<label>Output pin number :</label>
			<input type="text" placeholder="Output pin number..." id="call_python_output_pin" value="<?php echo get_option( 'python_button_output_pin');?>"/>
			<label>Speed of displaying morse code :</label>
			<input type="text" placeholder="Speed of displaying morse code..." id="call_python_speed" value="<?php echo get_option( 'python_button_speed');?>"/>
			<label>Twitter Hashtag to fetch feeds when Twitter mode is on (enter single hashtag without hash):</label>
			<input type="text" placeholder="Twitter Hashtag to fetch feeds..." id="twitter_fetch_hash_tag" value="<?php echo get_option( 'twitter_fetch_hash_tag');?>"/>
			<div>
				<label>Raspberry Pi Mode:</label>
				<label>
					<input type="radio" name="rpi_mode" id="mode_1" value="single_pin" <?php echo ($mode == "single_pin" ? "checked" : "");?> />
					Single Pin Mode
				</label>
				<label>
					<input type="radio" name="rpi_mode" id="mode_2" value="26_pin_mode" <?php echo ($mode == "26_pin_pin" ? "checked" : "");?> />
					26 Pin Mode
				</label>
			</div>
			<label>
				Twitter Mode on ?
				<input type="checkbox" name="twitter_mode" id="twitter_mode" value="on" <?php echo ($twitter_mode == "on" ? "checked" : "");?> /> Yes
			</label>
			<br/>
			<div>
				<label>Images for each pin to display on website:</label>
				<div class="pin_images_wrapper">
				<?php
					$characters = array(2 => "A", 3 => "B", 4 => "C", 5 => "D", 6 => "E", 7 => "F", 8 => "G", 9 => "H", 10 => "I", 11 => "J", 12 => "K", 13 => "L", 14 => "M", 15 => "N", 16 => "O", 17 => "P", 18 => "Q", 19 => "R", 20 => "S", 21 => "T", 22 => "U", 23 => "V", 24 => "W", 25 => "X", 26 => "Y", 27 => "Z");
					$uploads = wp_upload_dir();
					$pin_images_url = $uploads['baseurl']."/rpie/";
					$pin_images_path = str_replace($uploads['subdir'], "", $uploads['path']."/rpie/");

					$pinImages = json_decode(get_option('python_pin_uploads'),true);
					for($i=2 ; $i<=27 ; $i++) {
						echo '<div class="pin_images">';
						echo '<label>Pin - '.$i.' ('.$characters[$i].') - </label><input type="file" name="pinImage['.$i.']" value="" />';
						if(isset($pinImages[$i]))
						{
							echo '<img src="'.$pin_images_url.$pinImages[$i].'" alt="'.$i.'"/>';
						}
						echo '</div>';
					}
				?>
				</div>
			</div>
			<input type="button" name="call_python" value="Save settings for python" id="call_python"/>
			<input type="hidden" name="action" value="post_call_for_python" />
			<div id="success_response_msg"></div>
		</div><!-- #primary -->
	</form>
</div><!-- .wrap -->
<script type="text/javascript">
 	<?php 
 		echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
	?>
	jQuery( document ).on( 'click', '#call_python', function() {
		var fd = new FormData(jQuery( 'form#raspberrypi' )[0]);
		jQuery.ajax({
			processData: false,
      		contentType: false,
			url : ajaxurl,
			type : 'post',
			data : fd,
			// {
			// 	action : 'post_call_for_python',
			// 	message : jQuery('#call_python_text').val(),
			// 	time : jQuery('#call_python_time').val(),
			// 	output_pin : jQuery('#call_python_output_pin').val(),
			// 	speed : jQuery('#call_python_speed').val(),
			// 	rpi_mode : jQuery('input[name=rpi_mode]:checked').val(),
			// 	twitter_mode : (jQuery('#twitter_mode:checked').length ? "on" : "off"),
			// 	hashtag : jQuery('#twitter_fetch_hash_tag').val(),
			// },
			success : function( response ) {
				jQuery('#success_response_msg').html( response ).slideDown();
				setTimeout(function() {
					jQuery('#success_response_msg').html('').slideUp();
				},3000);
			}
		});
	});
</script>
<?php get_footer();
