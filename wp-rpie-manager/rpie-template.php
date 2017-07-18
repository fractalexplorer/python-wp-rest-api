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
		<input type="button" name="call_python" value="Save settings for python" id="call_python"/>
		<div id="success_response_msg"></div>
	</div><!-- #primary -->
</div><!-- .wrap -->
<script type="text/javascript">
 	<?php 
 		echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
	?>
	jQuery( document ).on( 'click', '#call_python', function() {
		jQuery.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				action : 'post_call_for_python',
				message : jQuery('#call_python_text').val(),
				time : jQuery('#call_python_time').val(),
				output_pin : jQuery('#call_python_output_pin').val(),
				speed : jQuery('#call_python_speed').val(),
				rpi_mode : jQuery('input[name=rpi_mode]:checked').val(),
				twitter_mode : (jQuery('#twitter_mode:checked').length ? "on" : "off"),
				hashtag : jQuery('#twitter_fetch_hash_tag').val(),
			},
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
